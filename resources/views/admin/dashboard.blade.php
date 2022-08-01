@extends('admin.layouts.dashboard')

    @section('content')

        <div class="dashboardGridContainer">

            <div class="gridCardRow">
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-cyan rounded-circle mb-md"><i class="fas fa-user"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $userCount }}</h3>
                            <h6 class="heading-muted mb-xl">Users</h6><a class="btn btn-opacity-cyan rounded btn-sm d-flex" href="/admin/user/">Show Users</a>
                        </div>
                    </div>
                </div>
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-primary rounded-circle mb-md"><i class="fas fa-ship"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $vesselCount }}</h3>
                            <h6 class="heading-muted mb-xl">Vessels</h6><a class="btn btn-opacity-primary rounded btn-sm d-flex" href="/admin/vessel/">Show Vessels</a>
                        </div>
                    </div>
                </div>
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-red rounded-circle mb-md"><i class="fas fa-address-book"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $bookingCount}}</h3>
                            <h6 class="heading-muted mb-xl">Bookings</h6><a class="btn btn-opacity-red rounded btn-sm d-flex" href="/admin/booking/">Show Bookings</a>
                        </div>
                    </div>
                </div>
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-green rounded-circle mb-md"><i class="fas fa-clock"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $scheduleCount }}</h3>
                            <h6 class="heading-muted mb-xl">Schedules</h6><a class="btn btn-opacity-green rounded btn-sm d-flex" href="/admin/schedule/">Show Schedules</a>
                        </div>
                    </div>
                </div>
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-purple rounded-circle mb-md"><i class="fas fa-route"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $routeCount }}</h3>
                            <h6 class="heading-muted mb-xl">Routes</h6><a class="btn btn-opacity-purple rounded btn-sm d-flex" href="/admin/route/">Show Routes</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gridLineChartRow">
                <canvas id="chLine" style="width: 600px; height: 600px"></canvas>
            </div>
            <div class="gridPieChartRow">
                <canvas id="pieChart" width="280" height="150"></canvas>
            </div>
            <div class="gridPieChartRowTwo">
                <canvas id="pieChartTwo" width="280" height="150"></canvas>
            </div>
        </div>

        @section('js_dashboard_chart')
            <script>
                
                $(document).ready(function() {
                    //line chart
                    var lineChartData = {
                        labels: ['Jan','Feb','Mar','Apr','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        datasets: [{
                            data: [189, 245, 383, 403, 589, 692, 734, 889, 945, 1083, 1103, 1289],
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(219, 242, 242,0.5)',
                            tension: 0.1
                        }]
                    };
                    var chLine = document.getElementById("chLine");
                    if (chLine) {
                        new Chart(chLine, {
                            type: 'line',
                            data: lineChartData,
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: false
                                        }
                                    }]
                                },
                                responsive: true,
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: true,
                                    text: 'This Year Sales'
                                }
                            }
                        });
                    }
                    //pie chart 1
                    var ctx = $("#pieChart");
                    var myLineChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Admin", "Staff", "Merchant", "Agent"],
                            datasets: [{
                                data: [{{$admin}}, {{$staff}}, {{$merchant}}, {{$agent}}],
                                backgroundColor: ["rgba(255, 0, 0, 0.5)", "rgba(200, 50, 255, 0.5)", "rgba(0, 100, 255, 0.5)", "rgba(100, 255, 0, 0.5)"]
                            }]
                        },
                        options: {
                            responsive: true,
                            legend: {
                                position: 'left',
                                //display: false,
                                labels: {
                                    fontColor: "black",
                                    boxWidth: 20,
                                    padding: 20
                                }
                            },
                            title: {
                                display: true,
                                text: 'Users with roles'
                            }
                        }
                    });

                    //pie chart 2
                    var ctx = $("#pieChartTwo");
                    var myLineChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Ferry", "Speed Boat"],
                            datasets: [{
                                data: [{{$ferry}}, {{$speedBoat}}],
                                backgroundColor: ["rgba(86, 226, 207, 0.5)", "rgba(86, 104, 226, 0.5)"]
                            }]
                        },
                        options: {
                            responsive: true,
                            legend: {
                                position: 'left',
                                //display: false,
                                labels: {
                                    fontColor: "black",
                                    boxWidth: 20,
                                    padding: 20
                                }
                            },
                            title: {
                                display: true,
                                text: 'Vessels with vessel type'
                            }
                        }
                    });
                });
            </script>
        @endsection
    @endsection

