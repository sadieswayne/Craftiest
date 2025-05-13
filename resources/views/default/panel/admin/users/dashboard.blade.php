@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('User Dashboard'))

@section('content')
    <div class="py-10">
        <div class="flex flex-col gap-11">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3 lg:gap-8 xl:grid-cols-3">
                <!-- Total Sales Card -->
                <x-card class="lqd-statistic-card w-full" size="sm">
                    <div class="flex gap-4">
                        <x-lqd-icon class="bg-background text-heading-foreground dark:bg-foreground/5" size="xl">
                            <x-tabler-currency-dollar class="size-6" stroke-width="1.5"/>
                        </x-lqd-icon>
                        <div class="lqd-statistic-info grow">
                            <p class="lqd-statistic-title mb-1 text-2xs font-medium text-heading-foreground/50">
                                {{ __('Total Registered Users') }}
                            </p>
                            <h3 class="lqd-statistic-change m-0 flex items-center gap-2 text-xl">
                                {{$totalUser}}
                                <x-change-indicator value="{{$newUsersPercentage}}"/>
                            </h3>
                        </div>
                    </div>
                </x-card>

                <!-- Online Users Card -->
                <x-card class="lqd-statistic-card w-full" size="sm">
                    <div class="flex gap-4">
                        <x-lqd-icon class="bg-background text-heading-foreground dark:bg-foreground/5" size="xl">
                            <x-tabler-user class="size-6" stroke-width="1.5"/>
                        </x-lqd-icon>
                        <div class="lqd-statistic-info grow">
                            <p class="lqd-statistic-title mb-1 text-2xs font-medium text-heading-foreground/50">
                                {{ __('Online Users') }}
                            </p>
                            <h3 class="lqd-statistic-change m-0 flex items-center gap-2 text-xl">
                                {{$onlineUsers}}
                            </h3>
                        </div>
                    </div>
                </x-card>

                <!-- Visitors Today Card -->
                <x-card class="lqd-statistic-card w-full" size="sm">
                    <div class="flex gap-4">
                        <x-lqd-icon class="bg-background text-heading-foreground dark:bg-foreground/5" size="xl">
                            <x-tabler-eye class="size-6" stroke-width="1.5"/>
                        </x-lqd-icon>
                        <div class="lqd-statistic-info grow">
                            <p class="lqd-statistic-title mb-1 text-2xs font-medium text-heading-foreground/50">
                                {{ __('Visitors Today') }}
                            </p>
                            <h3 class="lqd-statistic-change m-0 flex items-center gap-2 text-xl">
                                {{$todayVisitor}}
                            </h3>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
    <div class="py-10">
        <div class="flex flex-col lg:flex-row gap-11">
            <div id="container" class="w-full lg:w-2/3" style="height: 450px;"></div>
            <div id="country-list" class="w-full lg:w-1/3"></div>
        </div>
    </div>
    <div class="py-10">
        <div id="monthly-registered-users-chart" style="height: 400px;"></div>
    </div>
    <div class="py-10">
        <div>
            <h3>Total Users</h3>
            <h3>{{ number_format($totalYearCount) }}</h3>
        </div>
        <div id="yearly-registered-users-chart" style="height: 400px;"></div>
    </div>
@endsection

<script src="https://code.highcharts.com/maps/highmaps.js"></script>
<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
<script src="https://code.highcharts.com/mapdata/custom/world.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const data = @json($countryData);
        const formattedData = data.map(item => ({
            'hc-key': item.code.toLowerCase(),
            value: item.value
        }));

        Highcharts.mapChart('container', {
            chart: {
                map: 'custom/world'
            },
            title: {
                text: 'Registered User Countries'
            },
            colorAxis: {
                min: 1,
                type: 'logarithmic',
                minColor: '#E6E7E8',
                maxColor: '#005645'
            },
            series: [{
                data: formattedData,
                name: 'Users',
                states: {
                    hover: {
                        color: '#BADA55'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: '{point.name}: {point.value} users'
                }
            }]
        });

        const countryList = document.getElementById('country-list');
        countryList.innerHTML = '<h3>Top 30 Countries</h3>';
        const sortedData = data.sort((a, b) => b.value - a.value).slice(0, 30);
        sortedData.forEach(country => {
            countryList.innerHTML += `<p>${country.name} - ${country.value}</p>`;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const data = @json(array_values($data));
        Highcharts.chart('monthly-registered-users-chart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'New Registered Users (Current Month)'
            },
            xAxis: {
                categories: Array.from({length: {{ count($data) }}}, (_, i) => i + 1),
                title: {
                    text: ''
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                }
            },
            series: [{

                data: data
            }],
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: 'Users: <b>{point.y}</b>'
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monthlyUserCounts = @json(array_values($monthlyUserCounts));
        Highcharts.chart('yearly-registered-users-chart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Total Registered Users (Current Year)'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                title: {
                    text: ''
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                }
            },
            series: [{
                name: 'Total Users',
                data: monthlyUserCounts
            }],
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: 'Users: <b>{point.y}</b>'
            }
        });
    });
</script>