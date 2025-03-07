<form action="{{ route('platform.select.city') }}" method="POST">
    @csrf
    <select name="city_id" onchange="this.form.submit()">
        @foreach(\App\Models\City::all() as $city)
            <option value="{{ $city->id }}" {{ session('selected_city') == $city->id ? 'selected' : '' }}>
                {{ $city->name }}
            </option>
        @endforeach
    </select>
</form>
