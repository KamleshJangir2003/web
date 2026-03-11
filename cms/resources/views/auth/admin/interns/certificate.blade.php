<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Internship Certificate</title>

<style>

@page { margin:0; }

body{
margin:0;
padding:0;
font-family: "Times New Roman", serif;
background:#f3f5fa;
}

.wrapper{
display:flex;
align-items:center;
justify-content:center;

}

.certificate{


background:white;
padding:80px;
border:15px solid #d4af37;
outline:5px solid #f1e3a2;
position:relative;

box-shadow:0 20px 70px rgba(0,0,0,0.15);
}

/* watermark */

.certificate::after{

content:"KWIKSTER";
position:absolute;
top:45%;
left:50%;
transform:translate(-50%,-50%);
font-size:120px;
color:rgba(0,0,0,0.04);
letter-spacing:10px;
font-weight:bold;

}

/* header */

.header{
text-align:center;
margin-bottom:40px;
}

.logo{

font-size:42px;
font-weight:bold;
letter-spacing:6px;
color:#2c3e50;

}

.subtitle{

font-size:28px;
margin-top:10px;
color:#d4af37;
letter-spacing:4px;

}

.divider{

width:180px;
height:3px;
background:#d4af37;
margin:25px auto;

}

/* content */

.content{
text-align:center;
line-height:1.9;
font-size:18px;
}

.intern-name{

font-size:40px;
font-weight:bold;
color:#1a1a1a;
margin:25px 0;

border-bottom:2px solid #d4af37;
display:inline-block;
padding-bottom:8px;

}

.course{

font-size:24px;
font-weight:bold;
color:#d4af37;
margin-top:10px;

}

.details{

margin-top:30px;
font-size:16px;

}

/* seal */

.seal{

position:absolute;
right:90px;
bottom:160px;

width:110px;
height:110px;

border:6px double #d4af37;
border-radius:50%;

display:flex;
align-items:center;
justify-content:center;

font-size:12px;
text-align:center;
color:#d4af37;
font-weight:bold;

}

/* footer */

.footer{

margin-top:90px;
display:flex;
justify-content:space-between;

}

.signature{

text-align:center;
width:250px;

}

.signature-line{

border-top:2px solid #333;
margin-bottom:10px;

}

.signature p{
margin:4px;
}

/* issue date */

.issue{

text-align:center;
margin-top:40px;
font-size:14px;
color:#555;

}

.cert-id{

font-size:12px;
color:#999;
}

</style>

</head>

<body>

<div class="wrapper">

<div class="certificate">

<div class="header">

<div class="logo">KWIKSTER</div>

<div class="subtitle">
INTERNSHIP CERTIFICATE
</div>

<div class="divider"></div>

</div>


<div class="content">

<p>This is to certify that</p>

<div class="intern-name">
{{ $intern->name }}
</div>

<p>has successfully completed the internship program in</p>

<div class="course">
{{ $intern->course ?? 'Software Development' }}
</div>

<div class="details">

<p><strong>Duration :</strong> {{ $intern->internship_duration ?? 'N/A' }} Months</p>

<p>
<strong>Period :</strong>

{{ $intern->start_date ? \Carbon\Carbon::parse($intern->start_date)->format('d M Y') : 'N/A' }}

to

{{ $intern->completion_date ? \Carbon\Carbon::parse($intern->completion_date)->format('d M Y') : 'N/A' }}

</p>

@if($intern->performance_rating)
<p><strong>Performance :</strong> {{ $intern->performance_rating }}</p>
@endif

@if($intern->mentor)
<p><strong>Mentor :</strong> {{ $intern->mentor->full_name }}</p>
@endif

</div>

<p style="margin-top:30px">

We appreciate the dedication and professionalism shown during the internship period.

</p>

</div>


<!-- Seal -->

<div class="seal">
Verified <br> Certificate
</div>



<div class="footer">

<div class="signature">

<div class="signature-line"></div>

<p><strong>HR Manager</strong></p>

@if($intern->hr)
<p>{{ $intern->hr->full_name }}</p>
@endif

</div>


<div class="signature">

<div class="signature-line"></div>

<p><strong>Mentor</strong></p>

@if($intern->mentor)
<p>{{ $intern->mentor->full_name }}</p>
@endif

</div>

</div>


<div class="issue">

<p>

Issued on :

{{ $intern->completion_date ? \Carbon\Carbon::parse($intern->completion_date)->format('d F Y') : now()->format('d F Y') }}

</p>

<p class="cert-id">
Certificate ID : KIO{{ str_pad($intern->certificate_no,3,'0',STR_PAD_LEFT) }}
</p>

</div>


</div>
</div>

</body>
</html>