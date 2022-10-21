<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="user-profile-header-banner">
                <img src="{{asset('assets/img/jgu.jpg')}}" class="rounded-top" width="100%" height="250px"
                    style="object-fit: cover;">
            </div>
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                    <img src="{{ Auth::user()->image() }}"
                        class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="100px">
                </div>
                <div class="flex-grow-1 mt-4">
                    <div
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4>{{ Auth::user()->name_with_title }}</h4>
                            <ul
                                class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                <li class="list-inline-item fw-semibold">
                                    {{ Auth::user()->email }}
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('user.profile_by_id', ['id'=> Crypt::encrypt(Auth::user()->id) ]) }}" class="btn btn-primary text-nowrap">
                            <i class='bx bx-user-check'></i> {{ Auth::user()->username }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Header -->