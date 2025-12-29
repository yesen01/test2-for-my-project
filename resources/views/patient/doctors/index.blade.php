@extends('layout')

@section('content')
<main class="page">
    <section class="panel">
        <h1 class="title">Doctors</h1>

        <div class="grid">
            @foreach($doctors as $doctor)
                <article class="card">
                    <h3 class="card-title">{{ $doctor->name }}</h3>
                    <a class="btn" href="{{ route('patient.doctors.show', $doctor) }}">View Slots</a>
                </article>
            @endforeach
        </div>
    </section>
</main>
@endsection
