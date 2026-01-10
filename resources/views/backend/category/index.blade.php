<x-backend.layouts.main title="{{ 'Қатегориялар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <a href="{{ route('category.create') }}" class="btn btn-primary w-100 w-md-auto">
                            {{ 'Яратиш' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="categoryFilterForm" method="GET" action="{{ route('category.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th>{!! sortLink('parent_id', 'Ота категория') !!}</th>
                                <th>{!! sortLink('image', 'Расм') !!}</th>
                                <th>{!! sortLink('type', 'Тури') !!}</th>
                                <th>{!! sortLink('user_id', 'Ҳодим') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th>
                                    <select name="filters[parent_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($categoryParents as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.parent_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[image]" value="{{ request('filters.image') }}"
                                           class="form-control form-control-sm w-100" style="display: none;"></th>
                                <th>
                                    <select name="filters[type]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(\App\Models\Category::getTypeList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.type') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[user_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                              <th>
                                  <div class="d-flex">
                                      <input type="date" name="filters[created_from]"
                                             value="{{ request('filters.created_from') }}"
                                             class="form-control form-control-sm me-1" placeholder="From">
                                      <input type="date" name="filters[created_to]"
                                             value="{{ request('filters.created_to') }}"
                                             class="form-control form-control-sm" placeholder="To">
                                  </div>
                                </th>

                                @if(session('date_format_errors'))
                                    <div class="alert alert-danger mt-2">
                                        <ul>
                                            @foreach(session('date_format_errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <th>
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i
                                            class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($categories as $category)
                                <tr class="text-center" id="row-{{ $category->id }}">
                                    <td class="col-id">{{ $category->id }}</td>
                                    <td>{{ $category->title }}</td>
                                    <td>{{ $category->parent->title ?? ' '}}</td>
                                    <td>
                                        @if(optional($category->file)->path)
                                            <img src="{{ asset('storage/' . $category->file->path) }}" alt="Image"
                                                 style="width: 50px; height: auto;">
                                        @else

                                        @endif
                                    </td>
                                    <td style="width: 100px">{{ \App\Models\Category::getTypeList()[$category->type] ?? '-' }}</td>
                                    <td>{{ optional($category->user)->username }}</td>
                                    <td class="col-date">{{ $category->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="category" :id="$category->id" :view="true" :edit="true"/>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                   class="form-control form-control-sm me-1" placeholder="Категория номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($categories as $category)
                            <div class="card border">
                                <div class="card-body">
                                    <div class="text-center mb-2">
                                        @if(optional($category->file)->path)
                                            <img src="{{ asset('storage/' . $category->file->path) }}" alt="Image"
                                                 style="width: 50px; height: auto;">
                                        @else

                                        @endif
                                    </div>
                                    <p class="card-text ">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $category->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!} </strong>{{ $category->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('parent_id', 'Ота категория:') !!} </strong>{{ $category->parent->title ?? ' ' }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at_exact', 'Яратилди:') !!} </strong> {{ $category->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="category" :id="$category->id" :view="true" :edit="true"/>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $categories->links('pagination::bootstrap-4') }}
                </div>

                <style>
                    .card-stats {
                        border-radius: 12px;
                        padding: 20px;
                        color: #fff;
                        transition: 0.3s ease;
                        text-align: center;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    .card-stats:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 12px 24px rgba(0,0,0,0.3);
                    }
                    .card-stats.count {
                        background: linear-gradient(135deg, #00b894 35%, #2ecc71 65%);
                        border-left: 5px solid #00d68f;
                    }

                    .card-stats h5 {
                        font-weight: 700;
                        margin-bottom: 8px;
                        font-size: 1.25rem;
                    }
                    .card-stats p {
                        margin: 2px 0;
                        font-size: 0.95rem;
                    }
                    .card-stats i {
                        font-size: 2.2rem;
                        opacity: 0.7;
                    }
                </style>
                <div class="row mt-4">
                    <div class="col-md-12 mb-3">
                        <div class="card-stats count">
                            <div class="w-100">
                                <p>Қатегориялар</p>
                                <h5><strong>{{ number_format($categoryCount, 0, '', ' ') }} та</strong></h5>
                            </div>
                            <div>
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('categoryFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
            this.querySelectorAll('input[name="filters[title]"]').forEach(select => {
                if (select.offsetParent === null) {
                    select.disabled = true;
                }
            });

            // Bo‘sh input/selectlarni olib tashlaymiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value || !input.value.trim()) {
                    input.removeAttribute('name'); // name olib tashlanadi
                }
            });
        });
    </script>

</x-backend.layouts.main>
