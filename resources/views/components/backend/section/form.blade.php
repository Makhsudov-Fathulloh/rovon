<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif
    <div class="container-fluid mt-4">
        <div class="row">

            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="tab-content" style="margin: 0">
                            <div class="tab-pane fade show active">
                                <div class="pt-4">

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Номи</label>
                                        <input type="text" id="title" name="title" class="form-control"
                                               value="{{ old('title', $section->title ?? '') }}">
                                        @error('title')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Тавсифи</label>
                                        <textarea id="description"
                                                  name="description"
                                                  class="form-control ckeditor"
                                                  rows="10">
                                                {{ old('description', $section->description ?? '') }}
                                        </textarea>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">

                        <div class="col-md-11 mb-3">
                            <label for="organization_id">Филиал</label>
                            <select name="organization_id" id="organization_id" class="form-control" required>
                                <option value="">Филиални танланг</option>
                                @foreach($organizations as $id => $title)
                                    <option value="{{ $id }}"
                                        {{ old('organization_id', $section->organization_id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organization_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-11 mb-3">
                          <label for="previous_id">Олдинги бўлим</label>

                          <select name="previous_id" id="previous_id" class="form-control" disabled>
                              <option value="">Барчаси</option>
                          </select>
                          {{-- <select name="previous_id" id="previous_id" class="form-control" disabled>
                              <option value="">Барчаси</option>
                              @foreach($sections as $id => $title)
                                  <option value="{{ $id }}"
                                      {{ old('previous_id', $section->previous_id) == $id ? 'selected' : '' }}>
                                      {{ $title }}
                                  </option>
                              @endforeach
                          </select> --}}

                          @error('previous_id')
                              <div class="text-danger small">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="col-md-11 mb-3">
                            <label for="status" class="form-label">Статус</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (\App\Services\StatusService::getList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('status', $section->status ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (Route::currentRouteName() == 'section.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'section.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orgSelect = document.getElementById('organization_id');
    const prevSelect = document.getElementById('previous_id');

    function populatePrevious(sections, selectedId = null) {
        prevSelect.innerHTML = '<option value="">Барчаси</option>';
        for (const [id, title] of Object.entries(sections)) {
            const selected = selectedId == id ? 'selected' : '';
            prevSelect.innerHTML += `<option value="${id}" ${selected}>${title}</option>`;
        }
        prevSelect.disabled = false;
    }

    orgSelect.addEventListener('change', function() {
        const orgId = this.value;
        if (!orgId) {
            prevSelect.innerHTML = '<option value="">Барчаси</option>';
            prevSelect.disabled = true;
            return;
        }

        fetch(`/admin/organization/${orgId}/sections`)
            .then(response => {
                if (!response.ok) throw new Error('Server response was not ok');
                return response.json();
            })
            .then(data => populatePrevious(data, "{{ old('previous_id', $section->previous_id ?? '') }}"))
            .catch(err => {
                console.error(err);
                prevSelect.innerHTML = '<option value="">Барчаси</option>';
                prevSelect.disabled = true;
            });
    });

    // agar edit page bo‘lsa, oldindan fill qilish
    const initialOrgId = orgSelect.value;
    if (initialOrgId) {
        fetch(`/admin/organization/${initialOrgId}/sections`)
            .then(response => response.json())
            .then(data => populatePrevious(data, "{{ old('previous_id', $section->previous_id ?? '') }}"))
            .catch(err => {
                console.error(err);
                prevSelect.disabled = true;
            });
    }
});
</script>
