    <select class="form-control" name="annee" required>
        @for ($y = 2022; $y <= date('Y') + 1; $y++)
            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                {{ $y }}</option>
        @endfor
    </select>
