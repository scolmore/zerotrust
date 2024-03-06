@extends('zero-trust::master')

@section('content')
    <div>
        <div class="font-bold text-lg mb-3">Sign in with:</div>

        <div class="space-y-3">
            @foreach($directories as $key => $directory)
                <div class="mx-5">
                    <a href="{{ route('zero-trust.select-directory', ['directory' => $key]) }}">
                        <div
                            class="bg-gray-200 p-4 rounded border border-gray-400 cursor-pointer hover:opacity-75">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                         width="20"
                                         height="20"
                                         viewBox="0 0 17 17">
                                        <path
                                            d="M0 2.339l6.967-0.959v6.732h-6.967v-5.773zM0 14.661l6.967 0.959v-6.65h-6.967v5.691zM7.734 1.277v6.835h9.266v-8.112l-9.266 1.277zM7.734 15.723l9.266 1.277v-8.030h-9.266v6.753z"
                                            fill="#000000"/>
                                    </svg>
                                </div>

                                <div>Azure AD ・ {{ $directory['name'] ?? 'Name not set' }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
