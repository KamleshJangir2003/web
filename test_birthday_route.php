// Add this route temporarily in web.php to test birthday data

Route::get('/test-birthday', function() {
    $todayBirthdays = \App\Models\Employee::whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [date('m-d')])
        ->whereNotNull('dob')
        ->get();
    
    return response()->json([
        'today_date' => date('m-d'),
        'count' => $todayBirthdays->count(),
        'birthdays' => $todayBirthdays->map(function($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->full_name ?? $emp->first_name . ' ' . $emp->last_name,
                'department' => $emp->department,
                'dob' => $emp->dob,
                'dob_format' => date('m-d', strtotime($emp->dob))
            ];
        })
    ]);
});
