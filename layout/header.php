<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
         body {
            background: url('../images/bg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Cal Sans', sans-serif;
        }

         .reviews-container {
        background-color: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        width: 100%;
        height: 100%;
    }
    
    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .reviews-title {
        font-size: 22px;
        font-weight: bold;
        color: #333;
    }
    
    .reviews-count {
        font-size: 14px;
        color: #777;
    }
    
    .review-card {
        background-color: #e8f4f5;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .reviewer-name {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    
    .review-text {
        font-size: 14px;
        color: #555;
        margin-bottom: 5px;
        line-height: 1.4;
    }
    
    .rating {
        color: #ffc107;
        letter-spacing: 2px;
        font-size: 18px;
    }

        #navbar {
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 10;
            overflow-y: auto;
            background-color: rgba(75, 216, 226, 0.75);
            width: 300px;
        }

        #navbar.show {
            display: flex;
            opacity: 1;
        }

        .menu-container {
            position: relative;
            display: inline-block;
        }

        #menu-button {
            cursor: pointer;
        }

        main {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
            padding-bottom: 2rem;
            padding-top: 120px;
            width: 100%;
        }

        main.shifted {
            margin-left: 200px;
        }

        .data-table-container {
            background-color: white;
            border-radius: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem;
            margin-top: 5rem;
            width: 100%;
            max-width: 800px;
            margin-left: 300px;
            
        }
        

        .action-button {
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            line-height: 1.25rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }


        header {
            background-color: #fff;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 20;
        }

        header .logo {
            margin-right: auto;
            margin-left: auto;
        }

        header .profile {
            display: flex;
            align-items: center;
        }

        #navbar nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
        }

        #navbar nav a {
            background-color: #fff;
            color: #000;
            margin-top: 10px;
            padding: 15px 15px;
            border-radius: 0.5rem;
            width: 90%;
            text-align: center;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;

        }
        #navbar nav a:hover {
            background-color: #e5e7eb;
        }
        #menu-button:hover{
            background-color:rgb(255, 255, 255);
        }

        .sidebar ul li a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-header.bg-danger-subtle {
            background-color:#1bb3bd !important; 
            color: white !important; 
    }
    </style>
</head>
</body>
</html>