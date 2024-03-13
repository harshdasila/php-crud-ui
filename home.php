<?php
include 'db_connect.php';
$user_role_id = $_SESSION["user_role_id"];
$sql = "SELECT
        COUNT(*) AS total_users,
        SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) AS male,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS created_today,
        SUM(CASE WHEN DATE(updated_at) = CURDATE() THEN 1 ELSE 0 END) AS updated_today
        FROM
        users";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);
$totalUsers = $data["total_users"];
$createdUsers = $data["created_today"];
$updatedUsers = $data["updated_today"];
$maleUsers = $data["male"];
$femaleUsers = $totalUsers - $maleUsers;
$malePercent = ($maleUsers / $totalUsers) * 100;
$femalePercent = ($femaleUsers / $totalUsers) * 100;

$sql_date = "SELECT
            creation_date,
            SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) AS male_count,
            SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) AS female_count
            FROM (
            SELECT
                DATE(created_at) AS creation_date,
                gender
            FROM
                users
            WHERE
                created_at >= DATE_SUB(CURDATE(), INTERVAL 9 DAY) AND
                created_at <= CURDATE()
            ) AS subquery
            GROUP BY
            creation_date
            ORDER BY
            creation_date
            ";
$result_date = mysqli_query($conn, $sql_date);
while ($row = mysqli_fetch_assoc($result_date)) {
    $data[] = $row;
}
$data = array_values($data);
$id = $_SESSION['id'];
$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <link href="css/dashboard.css" rel="stylesheet">
    <style>

        .dashboard-container {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }

        .dashboard-block {
            height: 150px;
            width: 220px;
            background-color: lightgrey;
        }

        /* .dashboard-block > h1{
            text-align: center;
            height: 50px;
            padding: 0;
        } */
        .number-data {
            text-align: center;
        }

        .number-data-container {    
            width: 220px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .text-container {
            height: 50px;
            background-color: #214139;
            display: flex;
            justify-content: center;
            align-items: center;
            /* align-content: ; */
        }

        .text-container>h1 {
            padding: 0;
        }

        #chart-1,
        #chart-2,
        #chart-3 {
            margin-top: 30px;
            margin-bottom: 10px;
            height: 350px;
            width: 720px;
        }

        .charts-container {
           
        }
        .right_side_content {
            max-height: calc(100vh - 225px); /* Adjust 100px as needed */
            overflow-y: auto;
        }

    </style>
</head>

<body>

    <div class="header">
        <div class="wrapper">
            <div class="logo"><a href="#"><img src="images/logo.png"></a></div>


            <div class="right_side">
                <ul>
                    <li>Welcome
                        <?php echo $firstName ?>
                    </li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
            <div class="nav_top">
                <ul>
                    <li class="active"><a href=" home.php ">Dashboard</a></li>
                    <li><a href=" list-users.php ">Users</a></li>
                    <li><a href="manage-contact.php ">Queries</a></li>
                    <?php if ($user_role_id == 1): ?>
                        <li><a href=" settings.php ">Settings</a></li>
                    <?php endif ?>
                    <!-- <li><a href=" geoloclist.php ">Configuration</a></li> -->
                </ul>

            </div>
        </div>
    </div>

    <div class="clear"></div>
    <div class="clear"></div>
    <div class="content">
        <div class="wrapper">
            
            <div class="left_sidebr">
                <ul>
                    <li><a href="#" class="dashboard active">Dashboard</a></li>
                    <li><a href="list-users.php" class="user">Users</a>
                        <!-- <ul class="submenu">
                            <li><a href="">Manage Users</a></li>

                        </ul> -->

                    </li>
                    <li><a href="" class="Setting"></a>
                        <ul class="submenu">
                            <li><a href="change-password.php">Change Password</a></li>
                            <?php if($user_role_id==1 || $user_role_id==2):?> <li><a href="manage-email.php">Manage Email Content</a></li>
                                <?php endif?>

                        </ul>

                    </li>
                    <li><a href="" class="social"></a>
                        <ul class="submenu">
                        <?php if ($user_role_id == 1): ?>
                                <li><a href="settings.php">Settings</a></li>
                            <?php endif ?>
                            <li><a href="manage-contact.php">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>

                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Dashboard</h1>
                <div class="list-contet">
                    <div id="errorMessageContainer" style="display: none;">
                        <div class="error-message-div error-msg">
                            <img src="images/unsucess-msg.png">
                            <strong>UnSuccess!</strong> Your Message hasn't been Sent
                        </div>
                    </div>
                    <!-- here -->
                    <div class="dashboard-container">
                        <div class="dashboard-block">
                            <div class="text-container">
                                <h1>Total Number of Users</h1>
                            </div>

                            <div class="number-data-container">
                                <h2 class="number-data">
                                    <?php echo $totalUsers; ?>
                                </h2>
                            </div>

                        </div>
                        <div class="dashboard-block">
                            <div class="text-container">
                                <h1>Users Updated Today</h1>
                            </div>

                            <div class="number-data-container">
                                <h2 class="number-data">
                                    <?php echo $updatedUsers; ?>
                                </h2>
                            </div>
                        </div>
                        <div class="dashboard-block">
                            <div class="text-container">
                                <h1>Users Added Today</h1>
                            </div>

                            <div class="number-data-container">
                                <h2 class="number-data">
                                    <?php echo $createdUsers; ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="charts-container">
                        <div id="chart-1">

                        </div>
                        <div id="chart-2">

                        </div>
                        <div id="chart-3">

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="footer">
        <div class="wrapper">
            <p>Copyright © 2014 yourwebsite.com. All rights reserved</p>
        </div>
    </div>

    <script>
        const maleRatio = <?php echo $malePercent ?>;
        const femaleRatio = <?php echo $femalePercent ?>;

        const data = <?php echo json_encode($data); ?>;
        const newData = data.slice(4);

        // const created_users = <?php $createdUsers ?>;
        // const updated_users = <?php $updatedUsers ?>;



        Highcharts.chart('chart-1', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Gender Ratio'
            },
            tooltip: {
                valueSuffix: '%'
            },
            subtitle: {

            },
            plotOptions: {
                series: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: [{
                        enabled: true,
                        distance: 20
                    }, {
                        enabled: true,
                        distance: -40,
                        format: '{point.percentage:.1f}%',
                        style: {
                            fontSize: '1.2em',
                            textOutline: 'none',
                            opacity: 0.7
                        },
                        filter: {
                            operator: '>',
                            property: 'percentage',
                            value: 10
                        }
                    }]
                }
            },
            series: [
                {
                    name: 'Percentage',
                    colorByPoint: true,
                    data: [
                        {
                            name: 'Female',
                            y: femaleRatio
                        },
                        {
                            name: 'Male',
                            sliced: true,
                            selected: true,
                            y: maleRatio
                        }
                    ]
                }
            ]
        });

        Highcharts.chart('chart-2', {
            chart: {
                type: 'spline'
            },
            title: {
                text: 'Users Added'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: newData.map(item => item?.creation_date), // Assuming data is an array of objects with a 'creation_date' property
                accessibility: {
                    description: 'DATES USERS ADDED'
                }
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                labels: {
                    format: '{value}'
                },
                tickPositions: [0, 5, 10, 15, 20] // Set custom tick positions
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                name: 'Male',
                marker: {
                    symbol: 'square'
                },
                data: newData.map(item => parseInt(item?.male_count))
            }, {
                name: 'Female',
                marker: {
                    symbol: 'diamond'
                },
                data: newData.map(item => parseInt(item?.female_count))
            }]
        });

        // Data retrieved from https://en.wikipedia.org/wiki/Winter_Olympic_Games
        Highcharts.chart('chart-3', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Total Users vs. Users Updated Today'
            },
            xAxis: {
                categories: ['Total Users', 'Users Updated Today']
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Count'
                }
            },
            tooltip: {
                headerFormat: '<b>{point.x}</b><br/>',
                pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            series: [{
                name: 'Total Users',
                data: [<?php echo $totalUsers; ?>] // Assuming $totalUsers holds the total user count
            }, {
                name: 'Users Updated Today',
                data: [<?php echo $updatedUsers; ?>] // Assuming $updatedUsers holds the count of users updated today
            }]
        });




    </script>

</body>

</html>