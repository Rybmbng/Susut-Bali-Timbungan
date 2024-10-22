
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="https://balitimbungan.id/resto_assets/assets/img/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.4.2/dist/cdn.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Benne&family=Jersey+10&family=Jersey+25+Charted&family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Sedan+SC&display=swap" rel="stylesheet">
    <style>
        .sidebar-icon {
            width: 20px;
            height: 20px;
        }
        body {
            background-color: #f3f4f6 !important; 
            font-family: 'Josefin Sans', sans-serif !important; 
            line-height: 1.6;
            letter-spacing: 0.025em;
            overflow-y: auto;
        }

    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal" x-data="{ sidebarOpen: false, susutMenuOpen: false }">
    <div class="flex md:hidden justify-between items-center p-4 bg-gray-900 text-white">
        <a href=""> <img src="https://balitimbungan.id/resto_assets/assets/img/favicon.png" alt="Admin Dashboard" class="h-8"> </a> 
        <button @click="sidebarOpen = !sidebarOpen" class="focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="flex">
<div :class="sidebarOpen ? 'block' : 'hidden md:block'" class="bg-gray-900 md:h-screen md:w-64 w-full md:relative absolute z-10">
    <a href="#"> <img src="https://balitimbungan.id/resto_assets/assets/img/favicon.png" alt="Admin Dashboard" class="mx-auto py-6"> </a> 

    <div x-data="{
        dateTime: '',

        init() {
            this.updateDateTime();
            setInterval(() => {
                this.updateDateTime();
            }, 1000); // Update every second
        },

        updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', timeZoneName: 'short' };
            this.dateTime = now.toLocaleString('id-ID', options); 
        }
    }">
        <p class="text-white text-center mb-2" x-text="dateTime"></p>
    </div>

    <?php
    date_default_timezone_set('Asia/Jakarta');
    $hour = date('H');
    $greeting = "";

    if ($hour >= 5 && $hour < 10) {
        $greeting = "Selamat pagi";
    } elseif ($hour >= 10 && $hour < 14) {
        $greeting = "Selamat siang";
    } elseif ($hour >= 14 && $hour < 18) {
        $greeting = "Selamat sore";
    } else {
        $greeting = "Selamat malam";
    }

    $username = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 'User'; 

    echo "<p class='text-white text-center mb-4'>$greeting, $username!</p>";
    ?>

            <nav class="mt-10 text-white">
  <a href="./dashboard.php" class="block py-2.5 px-4 flex justify-between items-center rounded transition duration-200 hover:bg-gray-700">
    <span class="flex items-center">
      <svg class="sidebar-icon fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" /> 
      </svg>
      <span class="ml-3">Home</span>
    </span>
  </a>
                <a href="#" @click="susutMenuOpen = !susutMenuOpen" class="block py-2.5 px-4 flex justify-between items-center rounded transition duration-200 hover:bg-gray-700">
                    <span class="flex items-center">
                        <svg class="sidebar-icon fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M3 4v16h18V4H3zm16 2v12H5V6h14zM7 8h3v8H7V8zm7 0h3v8h-3V8z"/>
                        </svg>
                        <span class="ml-3">Pengeluaran Lain</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform" :class="susutMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </a>
                <div x-show="susutMenuOpen" class="pl-8">
                    <a href="add_pl.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Tambah Pengeluaran Lain</a>
                    <a href="list_pl.php" class="block py-2 px-4 rounded transition duration-200 hover:bg-gray-700">List Pengeluaran Lain</a>
                </div>
                <a href="#" @click="susutMenuOpen = !susutMenuOpen" class="block py-2.5 px-4 flex justify-between items-center rounded transition duration-200 hover:bg-gray-700">
                    <span class="flex items-center">
                        <svg class="sidebar-icon fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M3 4v16h18V4H3zm16 2v12H5V6h14zM7 8h3v8H7V8zm7 0h3v8h-3V8z"/>
                        </svg>
                        <span class="ml-3">Transaksi</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform" :class="susutMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </a>
                <div x-show="susutMenuOpen" class="pl-8">
                    <a href="trans_terima_barang.php" class="block py-2 px-4 rounded transition duration-200 hover:bg-gray-700">Konfirm Terima</a>
                </div>
                <a href="logout.php" class="block py-2.5 px-4 mt-4 rounded transition duration-200 hover:bg-red-600">
                    <svg class="sidebar-icon fill-current text-gray-400 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M13 2v2H5v16h8v2H3V2h10zm6 7h-8v2h8v7h2V9h-2zm-3.586-5.414L19 4.586 15.414 8H21v2h-6V4h2v2.586z"/>
                    </svg>
            
                    <span class="ml-3">Logout</span>
                </a>
            </nav>
        </div>

</body>
</html>

