@if (session('success') || session('error') || $errors->any())
    <div class="wrap">
        @if (session('success'))
            <div class="flash flash--ok" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash--err" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash flash--err" role="alert">
                <strong>Ada isian yang perlu diperbaiki:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
