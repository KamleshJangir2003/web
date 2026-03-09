# FACE ATTENDANCE SYSTEM - FLOW DIAGRAM

## 📊 ATTENDANCE MARKING FLOW

```
┌─────────────────────────────────────────────────────────────┐
│                    EMPLOYEE SCANS FACE                      │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Face Detection & Recognition                   │
│              (face-api.js matching)                         │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Get Employee Shift Information                 │
│              (shift_id → shift_types table)                 │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Calculate Attendance Date (for night shift)         │
│         - Day Shift: Current date                           │
│         - Night Shift: Shift start date                     │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Check if Attendance Exists for Today                │
└────────────────────────┬────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         │                               │
         ▼                               ▼
┌──────────────────┐            ┌──────────────────┐
│  NO ATTENDANCE   │            │ ATTENDANCE EXISTS│
│     FOUND        │            │                  │
└────────┬─────────┘            └────────┬─────────┘
         │                               │
         ▼                               ▼
┌──────────────────┐            ┌──────────────────┐
│   CHECK-IN       │            │ Check check_out  │
│                  │            │     status       │
│ 1. Create record │            └────────┬─────────┘
│ 2. Set check_in  │                     │
│ 3. Calculate     │         ┌───────────┴──────────┐
│    late_minutes  │         │                      │
│ 4. Set status    │         ▼                      ▼
│    (Late/Present)│  ┌──────────────┐    ┌──────────────┐
└────────┬─────────┘  │ check_out    │    │ check_out    │
         │            │   IS NULL    │    │  NOT NULL    │
         │            └──────┬───────┘    └──────┬───────┘
         │                   │                   │
         │                   ▼                   ▼
         │            ┌──────────────┐    ┌──────────────┐
         │            │  CHECK-OUT   │    │   ALREADY    │
         │            │              │    │  COMPLETED   │
         │            │ Update       │    │              │
         │            │ check_out    │    │ Return error │
         │            │ timestamp    │    │   message    │
         │            └──────┬───────┘    └──────┬───────┘
         │                   │                   │
         └───────────────────┴───────────────────┘
                             │
                             ▼
                  ┌──────────────────┐
                  │  Return Response │
                  │  to Frontend     │
                  └──────────────────┘
```

---

## 🕐 LATE CALCULATION LOGIC

```
┌─────────────────────────────────────────────────────────────┐
│                  Employee Check-In Time                     │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Get Shift Start Time from shift_types               │
│         Example: Day Shift = 09:30 AM                       │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Add Grace Period (late_after minutes)               │
│         Grace Time = Start Time + late_after                │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Compare Check-In Time with Grace Time               │
└────────────────────────┬────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         │                               │
         ▼                               ▼
┌──────────────────┐            ┌──────────────────┐
│  Check-In Time   │            │  Check-In Time   │
│  <= Grace Time   │            │  > Grace Time    │
└────────┬─────────┘            └────────┬─────────┘
         │                               │
         ▼                               ▼
┌──────────────────┐            ┌──────────────────┐
│  ON TIME         │            │  LATE            │
│                  │            │                  │
│ late_minutes = 0 │            │ late_minutes =   │
│ status = Present │            │ (check_in -      │
│                  │            │  start_time)     │
│                  │            │ status = Late    │
└──────────────────┘            └──────────────────┘

Example:
Shift Start: 09:30 AM
Grace Period: 0 minutes
Check-In: 09:45 AM
Late Minutes: 15
Status: Late
```

---

## 🌙 NIGHT SHIFT DATE HANDLING

```
┌─────────────────────────────────────────────────────────────┐
│              Night Shift: 07:30 PM - 05:10 AM               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Employee Check-In: March 9, 7:30 PM                 │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         System Detects: end_time < start_time               │
│         (05:10 < 19:30) = Night Shift                       │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Attendance Date = Shift Start Date                  │
│         Date = March 9                                      │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         Employee Check-Out: March 10, 5:10 AM               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         System Finds Attendance by Date = March 9           │
│         Updates check_out = March 10, 5:10 AM               │
│         Attendance Date REMAINS = March 9                   │
└─────────────────────────────────────────────────────────────┘

Result:
┌──────────────┬─────────────────────┬─────────────────────┬──────┐
│ Date         │ Check-In            │ Check-Out           │ Shift│
├──────────────┼─────────────────────┼─────────────────────┼──────┤
│ March 9      │ Mar 9, 7:30 PM      │ Mar 10, 5:10 AM     │ Night│
└──────────────┴─────────────────────┴─────────────────────┴──────┘
```

---

## 🗄️ DATABASE RELATIONSHIPS

```
┌─────────────────────┐
│   shift_types       │
│─────────────────────│
│ id (PK)             │
│ shift_name          │
│ start_time          │
│ end_time            │
│ late_after          │
└──────────┬──────────┘
           │
           │ 1:N
           │
           ▼
┌─────────────────────┐
│   employees         │
│─────────────────────│
│ id (PK)             │
│ employee_id         │
│ name                │
│ shift_id (FK)       │◄────┐
│ face_data           │     │
└──────────┬──────────┘     │
           │                │
           │ 1:N            │
           │                │
           ▼                │
┌─────────────────────┐     │
│   attendance        │     │
│─────────────────────│     │
│ id (PK)             │     │
│ employee_id (FK)    │─────┘
│ date                │
│ check_in            │
│ check_out           │
│ status              │
│ late_minutes        │
└─────────────────────┘
```

---

## 🎯 STATUS FLOW

```
┌──────────────┐
│  Check-In    │
└──────┬───────┘
       │
       ▼
┌──────────────────────────┐
│ Calculate Late Minutes   │
└──────┬───────────────────┘
       │
       ├─── late_minutes = 0 ──► Status = "Present"
       │
       └─── late_minutes > 0 ──► Status = "Late"


┌──────────────┐
│  Check-Out   │
└──────┬───────┘
       │
       ▼
┌──────────────────────────┐
│ Status Remains Same      │
│ (Present or Late)        │
└──────────────────────────┘
```

---

## 📱 FRONTEND DISPLAY

```
┌─────────────────────────────────────────────────────────────┐
│              Today's Attendance                             │
├──────────────┬──────────────┬──────────────┬───────────────┤
│ Employee ID  │ Name         │ Time         │ Status        │
├──────────────┼──────────────┼──────────────┼───────────────┤
│ KIO01        │ John Doe     │ 09:45 AM     │ 🟡 Late (15)  │
│              │              │ (Check-In)   │               │
├──────────────┼──────────────┼──────────────┼───────────────┤
│ KIO02        │ Jane Smith   │ 09:25 AM     │ 🟢 On Time    │
│              │              │ (Check-In)   │               │
├──────────────┼──────────────┼──────────────┼───────────────┤
│ KIO01        │ John Doe     │ 06:30 PM     │ 🔵 Checked Out│
│              │              │ (Check-Out)  │               │
└──────────────┴──────────────┴──────────────┴───────────────┘
```

---

## 🔄 COMPLETE USER JOURNEY

```
Day 1 - Morning
├─ 09:25 AM: Employee scans face
├─ System: Check-in successful (On Time)
├─ Status: Present, late_minutes = 0
│
Day 1 - Evening
├─ 06:30 PM: Employee scans face
├─ System: Check-out successful
├─ check_out timestamp updated
│
Day 1 - Night (Attempt 3)
├─ 08:00 PM: Employee scans face
├─ System: "Attendance already completed today"
└─ No changes made

Day 2 - Morning
├─ 09:45 AM: Employee scans face
├─ System: Check-in successful (Late)
├─ Status: Late, late_minutes = 15
└─ New attendance record created
```

This visual representation helps understand the complete flow!
