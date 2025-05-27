@extends('layouts.authentication.master')
@section('title', 'Login SSO SIAP')
@section('script')
<script type="text/javascript">
    const getRandomNumber = (limit) => {
        return Math.floor(Math.random() * limit);
    };
    const getRandomColor = () => {
        const h = getRandomNumber(360);
        return `hsl(${h}deg, 100%, 90%)`;
    };

    const setBackgroundColor = () => {
        const randomColor = getRandomColor();
        const randomColor2 = getRandomColor();
        document.body.style.backgroundImage = "linear-gradient(to bottom right, "+ randomColor +", "+ randomColor2 +")";
    };

    setBackgroundColor()

    setInterval(() => {
        setBackgroundColor();
    }, 1500);
</script>
@endsection
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-2">
                        <a href="{{ route('index') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img src="{{asset('assets/img/icons/siakadcloud2018.png')}}" width="175">
                            </span>
                        </a>

                    </div>
                    <br>
                    <!-- /Logo -->
                    <h4 class="mb-2 text-center text-bold">Masuk ke SSO</h4>
                    {{-- <p class="text-center">Masuk ke akun yang telah terdaftar untuk merevolusi dunia pendidikan yang lebih baik.</p> --}}
                    <div class="divider my-2">
                        <div class="divider-text mb-2">Single Sign On</div>
                    </div>
                    @error('msg')
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {!! $message !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    <form id="formAuthentication" class="mb-3" action="" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}"
                                placeholder="Masukkan email yang terdaftar di SIAP JGU" autofocus />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password" name="password">Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="Masukkan password" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <input type="hidden" name="urlintended" value="{{ (isset($_GET['redirect_to']) ? $_GET['redirect_to'] : null) }}">
                        </div>
                        <div class="mb-3">
                            <div class=" d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Ingat Saya') }} </label>
                                </div>
                                <a href="https://sso.sevima.com/users/password-reset">
                                    <small>Lupa Password?</small>
                                </a>
                            </div>
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-primary  w-100" type="submit" name="login"><i
                                    class="bx bx-log-in-circle me-2"></i>Masuk</button>
                        </div>

                    </form>
                    <small>
                        <center><i>Jika terdapat kendala masuk atau belum memiliki akun silakan menghubungi tim <a
                                    target="_blank" href="https://s.jgu.ac.id/m/itic">ITIC</a></i></center>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection
