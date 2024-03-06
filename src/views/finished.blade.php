@extends('zero-trust::master')

@section('content')
    <div>
        <div class="font-bold text-lg mb-3">Signed out</div>

        <div class="mb-3">
            You have been signed out and your session is finished. You may now close this tab.
        </div>

        <div>
            To start a new session <a href="/" target="_blank" class="font-bold hover:opacity-75">click here</a>
        </div>
    </div>
@endsection
