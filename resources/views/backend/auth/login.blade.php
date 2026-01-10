<x-backend.layouts.blank title="{{ 'Логин' }}">

    <div id="loginform">
        <div class="text-center pt-5">
          <span class="db">
              <img src="{{ asset('images/logo.png') }}" alt="logo" width="150"/>
              <img src="{{ asset('images/logo-text.png') }}" alt="logo" width="250"/>
          </span>
        </div>

        <!-- Form -->
        <form class="form-horizontal mt-5" id="login-form" action="{{ route('authenticate') }}" method="POST">
            @csrf
            <div class="row pb-4">
                <div class="col-12">
                    @error('email')
                    <div class="text-danger text-center mt-1 mb-2">{{ $message }}</div>
                    @enderror
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                      <span class="input-group-text bg-success text-white h-100" id="basic-addon1">
                                          <i class="mdi mdi-account fs-4"></i>
                                      </span>
                        </div>
                        <input type="email" name="email" class="form-control form-control-lg" aria-label="Email"
                               aria-describedby="basic-addon1"
                               placeholder="Э-почта" required=""/>

                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                      <span class="input-group-text bg-warning text-white h-100" id="basic-addon2">
                                          <i class="mdi mdi-lock fs-4"></i>
                                      </span>
                        </div>
                        <input type="password" name="password" class="form-control form-control-lg"
                               aria-label="Password" aria-describedby="basic-addon2"
                               placeholder="Парол" required=""/>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            <i class="bi bi-square me-1"></i> Эслаб қолиш
                        </label>
                    </div>
                </div>
            </div>
            <div class="row border-top border-secondary">
                <div class="col-12">
                    <div class="form-group">
                        <div class="pt-3">
                            <button class="btn btn-info" id="to-recover" type="button"><i
                                    class="mdi mdi-lock fs-4 me-1"></i>Паролни унутдингизми?
                            </button>
                            <button class="btn btn-success float-end text-white" type="submit">Кириш</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- end: Form -->
    </div>

    <div id="recoverform">
        <div class="text-center">
                      <span class="text-white">
                          Enter your e-mail address below and we will send you instructions how to recover a password.
                      </span>
        </div>

        <div class="row mt-3">
            <!-- Form -->
            <form class="col-12" action="#">
                @csrf
                <!-- email -->
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                                    <span class="input-group-text bg-danger text-white h-100" id="basic-addon1">
                                        <i class="mdi mdi-email fs-4"></i>
                                    </span>
                    </div>
                    <input type="email" class="form-control form-control-lg" placeholder="Э-почта манзили"
                           aria-label="Email" aria-describedby="basic-addon1"/>
                </div>

                <!-- pwd -->
                <div class="row mt-3 pt-3 border-top border-secondary">
                    <div class="col-12">
                        <a class="btn btn-success text-white" href="#" id="to-login" name="action">Логин саҳифасига
                            қайтиш</a>
                        <button class="btn btn-info float-end" type="button" name="action">Қайта тиклаш</button>
                    </div>
                </div>
                <!-- end: Form -->
            </form>
        </div>

    </div>

</x-backend.layouts.blank>
