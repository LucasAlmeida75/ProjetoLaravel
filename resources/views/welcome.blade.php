@extends('layouts.main')

@section('title', 'HDC Events')
@section('content')

    <h1>Welcome</h1>

    <p>
        <a href="/produtos">Produtos</a>
    </p>

    @foreach ($events as $event)
        <p>{{ $event->title }}</p>
    @endforeach

@endsection