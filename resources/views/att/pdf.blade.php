<html>

<head>
    <title>Event Attendances | {{ Date::now()->format('j F Y') }}
    </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        html {
            margin: 1.5cm;
        }

        .page-break {
            page-break-after: always;
        }

    </style>
    <style>
        tbody td {
            vertical-align: top;
            word-wrap: break-word;
        }

        td:nth-child(1) {
            max-width: 120px;
        }

    </style>
</head>

<body style="font-size: 11pt;">
    <table width="100%">
        <tr>
            <td width="50%" valign="top">
               
            </td>
            <td width="50%" style="text-align: right;">
                <img src="{{ public_path('assets/img/jgu.png') }}" style="height: 60px;" alt="">
            </td>
        </tr>
    </table>
    <br>
    <center>
        <b style="font-size:50px">{{$data->title}}</b>
        <br>
        <b style="font-size:100px">SCAN HERE</b>
        <br>
        <b style="font-size:30px;">BEFORE YOU JOIN THIS EVENT!</b>
        <br>
        <img src="{!! $qr !!}" style="height: 350px;margin:75px 0;">
        <br>
        <b style="font-size:50px ">{{$data->location}}</b>
    </center>
    <div class="card-body">
        <div class="row">
        <div class="inner-career-banner">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xxl-7 col-xl-8 col-lg-10 col-md-10">
            <div class="section-heading-14 text-center">
              <h2>Career & Opportunities</h2>
              <p>We offer an effective combination of broad customer service expertise and deep product knowledge
                to ensure customer experience</p>
            </div>
          </div>
          <div class="row feature-job-items justify-content-center">
            <div class="col-xl-4 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <div class="feature-job-box h-100">
                <div class="location d-flex align-items-center">
                  <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="content">
                    <span>New York, USA</span>
                  </div>
                </div>
                <h4>Full-Stuck Web Developer</h4>
                <p>By <span> Chorocon Ltd</span></p>
                <div class="time-apply-area">
                  <div class="d-xs-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="icon">
                        <i class="fas fa-calendar"></i>
                      </div>
                      <div class="content">
                        <span>1 Week ago</span>
                      </div>
                    </div>
                    <div class="apply-now-btn-fj">
                      <a href="career-details.html" class="btn btn-style-03 focus-reset">Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <div class="feature-job-box h-100">
                <div class="location d-flex align-items-center">
                  <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="content">
                    <span>Singapore</span>
                  </div>
                </div>
                <h4>Senior Project Manager</h4>
                <p>By <span> Google Inc</span></p>
                <div class="time-apply-area">
                  <div class="d-xs-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="icon">
                        <i class="fas fa-calendar"></i>
                      </div>
                      <div class="content">
                        <span>2 Week ago</span>
                      </div>
                    </div>
                    <div class="apply-now-btn-fj">
                      <a href="career-details.html" class="btn btn-style-03 focus-reset">Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <div class="feature-job-box h-100">
                <div class="location d-flex align-items-center">
                  <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="content">
                    <span>Remote</span>
                  </div>
                </div>
                <h4>Junior Graphic Designer</h4>
                <p>By <span> Canava</span></p>
                <div class="time-apply-area">
                  <div class="d-xs-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="icon">
                        <i class="fas fa-calendar"></i>
                      </div>
                      <div class="content">
                        <span>3 Week ago</span>
                      </div>
                    </div>
                    <div class="apply-now-btn-fj">
                      <a href="career-details.html" class="btn btn-style-03 focus-reset">Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <div class="feature-job-box h-100">
                <div class="location d-flex align-items-center">
                  <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="content">
                    <span>Singapore</span>
                  </div>
                </div>
                <h4>Senior Project Manager</h4>
                <p>By <span> Google Inc</span></p>
                <div class="time-apply-area">
                  <div class="d-xs-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="icon">
                        <i class="fas fa-calendar"></i>
                      </div>
                      <div class="content">
                        <span>2 Week ago</span>
                      </div>
                    </div>
                    <div class="apply-now-btn-fj">
                      <a href="career-details.html" class="btn btn-style-03 focus-reset">Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <div class="feature-job-box h-100">
                <div class="location d-flex align-items-center">
                  <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="content">
                    <span>Singapore</span>
                  </div>
                </div>
                <h4>Junior Graphic Designer</h4>
                <p>By <span> Canava</span></p>
                <div class="time-apply-area">
                  <div class="d-xs-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="icon">
                        <i class="fas fa-calendar"></i>
                      </div>
                      <div class="content">
                        <span>2 Week ago</span>
                      </div>
                    </div>
                    <div class="apply-now-btn-fj">
                      <a href="career-details.html" class="btn btn-style-03 focus-reset">Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <div class="feature-job-box h-100">
                <div class="location d-flex align-items-center">
                  <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="content">
                    <span>Singapore</span>
                  </div>
                </div>
                <h4>Full-Stact Web Developer</h4>
                <p>By <span> Chorocon Ltd.</span></p>
                <div class="time-apply-area">
                  <div class="d-xs-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="icon">
                        <i class="fas fa-calendar"></i>
                      </div>
                      <div class="content">
                        <span>1 Week ago</span>
                      </div>
                    </div>
                    <div class="apply-now-btn-fj">
                      <a href="career-details.html" class="btn btn-style-03 focus-reset">Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
        </div>
    </div>
    <hr class="my-0">
</body>

</html>