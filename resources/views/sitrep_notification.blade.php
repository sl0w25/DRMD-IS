<h2>Sitrep #{{ $sitrep->id }} Submitted</h2>
<p>{{ $messageContent }}</p>

<h3>Incident Details:</h3>
<ul>
    <li>Province: {{ $sitrep->province }}</li>
    <li>Municipality: {{ $sitrep->municipality }}</li>
    <li>Barangay: {{ $sitrep->barangay }}</li>
    <li>Incident Type: {{ $sitrep->incident_type }}</li>
</ul>
