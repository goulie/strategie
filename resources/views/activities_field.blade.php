<div class="form-group">
    <label>Type d'activité</label>
    <select class="form-control" name="activity_id" required>
        <option value="" disabled selected>-- Sélectionner --</option>
        @foreach (App\Models\DmsAgrCotisation::Activities() as $a)
            <option value="{{ $a->id }}">{{ $a->libelle }}</option>
        @endforeach
    </select>
</div>