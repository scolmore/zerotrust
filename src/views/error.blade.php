@extends('zero-trust::master')

@section('content')
    <div>
        <div class="font-bold text-lg mb-3">Error</div>

        <div class="mb-3 text-gray-600">
            An error has occurred while attempting to authenticate your azure active directory account.
        </div>

        <div>
            <span class="font-semibold">Message:</span>

            {{ $message ?? 'Unknown error' }}

            @if(isset($logout))
                <p class="mt-3">
                    Log out to try another account <a href="{{ $logout }}" class="font-bold">here</a>.
                </p>
            @endif
        </div>
    </div>
@endsection
