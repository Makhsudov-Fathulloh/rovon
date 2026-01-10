<x-backend.layouts.main title="{{ 'Файллар' }}">

    <div class="container-fluid">
        <div class="row el-element-overlay">
            @foreach($files as $file)
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="el-card-item">
                            <div class="el-card-avatar el-overlay-1">
                                <img src="{{ asset('storage/' . $file->path) }}" alt="{{ $file->name }}" />
                                <div class="el-overlay">
                                    <ul class="list-style-none el-info">
                                        <li class="el-item">
                                            <a class="btn default btn-outline image-popup-vertical-fit el-link"
                                               href="{{ asset('storage/' . $file->path) }}">
                                                <i class="mdi mdi-magnify-plus"></i>
                                            </a>
                                        </li>
                                        <li class="el-item">
                                            <a class="btn default btn-outline el-link" href="javascript:void(0);">
                                                <i class="mdi mdi-link"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="el-card-content">
                                <h4 class="mb-0">{{ $file->name }}</h4>
                                <span class="text-muted">{{ $file->title }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</x-backend.layouts.main>



