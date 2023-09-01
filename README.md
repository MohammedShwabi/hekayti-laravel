<p align="center">
  <a href="https://github.com/MohammedShwabi/hekayti-laravel">
 <img src="images/public/img/logo.png" alt="Logo" width="100" height="100"></a>
</p>

<h3 align="center">hekayti-laravel</h3>

<div align="center">

![Status][status-shield]
[![GitHub Pull Requests][pull-shield]][pull-url]
[![License][license-shield]][license-url]

</div>

<p align="center">
Laravel web panel for a playful educational app, enhancing children's reading and listening skills  in a fun way.
<br> 
</p>

## 📝 Table of Contents

- [About](#about)
- [Screenshots](#screenshots)
- [Built Using](#built_using)
- [Getting Started](#getting_started)
- [Prerequisites](#prerequisites)
- [Clone the Repository](#clone)
- [Install Dependencies](#dependencies)
- [Environment Setup](#environment)
- [Database Configuration](#db_config)
- [Storage Configuration](#storage_config)
- [Running the Project](#running_project)
- [API Reference](#api)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)
- [Authors](#authors)
- [Acknowledgments](#acknowledgement)

## 🧐 About <a name = "about"></a>

![web site story page][story-screenshot]

hekatyi_laravel: is a backend system designed to be a content management application for stories. It is used in a mobile application called "hekatyi" (My stroy in Arabic). 

The management structure is divided into two roles: 

- <b>main administrator:</b>  who controls everything in the application, including publishing stories and managing sub-managers, and view statics of the growth of users in mobile app.

- <b>sub-manager:</b> who is responsible for managing the stories and their content, such as images, sounds, and text. 

Additionally, users of the application can also edit their personal information.


<!-- :camera: -->
## 📷 Screenshots <a name = "screenshots"></a>
<b>Here are some screenshots of the project:</b>

<b>Login Page:</b>

![Login Page][login-screenshot]

<b>Story Page:</b>

![story page][story-screenshot]

<b>slide Page:</b>

![slide page][slide-screenshot]

<b>Admin Page:</b>

![admin page][admin-screenshot]

<b>Home Page:</b>

![home page][home-screenshot]

<b>Profile Page:</b>

![profile page][profile-screenshot]

## ⛏️ Built Using <a name = "built_using"></a>

* [![Laravel][Laravel.com]][Laravel-url]
* [![Bootstrap][Bootstrap.com]][Bootstrap-url]
* [![JQuery][JQuery.com]][JQuery-url]
* [![MySQL][MySQL.com]][MySQL-url]

<!-- :checkered_flag: -->
## 🏁 Getting Started <a name = "getting_started"></a>

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

<!-- :gear: -->
## ⚙️ Prerequisites <a name = "prerequisites"></a>

Before you begin, ensure you have the following:

- **PHP**: Make sure you have PHP installed on your system. You can download it from the [official PHP website](https://www.php.net/downloads.php).

- **Composer**: Composer is a dependency management tool for PHP. You can install it by following the instructions [here](https://getcomposer.org/download/).

- **MySQL**: This project requires a MySQL database. You can download and install it from the [official MySQL website](https://dev.mysql.com/downloads/installer/).

- **Git**: You'll need Git to clone the repository. Download and install it from the [official Git website](https://git-scm.com/downloads).

- **Laravel Requirements**: Make sure your system meets the [Laravel server requirements](https://laravel.com/docs/master/installation#server-requirements).

<!-- :open_file_folder: -->
## 📂 Clone the Repository <a name = "clone"></a>

Open your terminal/command prompt and run the following command to clone the repository:

```bash
git clone https://github.com/MohammedShwabi/hekayti-laravel.git
```

<!-- :rocket: -->
## 🚀 Install Dependencies  <a name = "dependencies"></a>
1. Navigate to the project directory:
```bash
cd hekayti-laravel
```
2. Install project dependencies:
```bash
composer install
```


<!-- :computer: -->
## 💻 Environment Setup <a name = "environment"></a>

1. Copy the .env.example file and create a .env file:
```bash
cp .env.example .env
```
2. Generate the application key:
```bash
php artisan key:generate
```

<!-- :floppy_disk: -->
## 💾 Database Configuration <a name = "db_config"></a>
1. Open the .env file and configure the database settings:
```js
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hekayti
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

3. Create the database:
<!-- php artisan migrate -->
```bash
php artisan migrate --seed
```

<!-- :file_folder: -->
## 📁 Storage Configuration <a name = "storage_config"></a>
Laravel uses storage for various purposes, including storing uploaded files, cached files, and more. 

Follow these steps to configure the storage:

1. copy the contents of the 'images' folder to the 'storage/app' directory:

```bash
cp -R images/* storage/app/
```

2. Create symbolic links for storage:
```bash
php artisan storage:link
```

<!-- :rocket: -->
## 🚀 Running the Project <a name = "running_project"></a>

1. Start the development server:
```bash
php artisan serve
```
The project should now be accessible in your browser at http://localhost:8000.

2. enter the following credential to login to the web panel:
```js
email: admin@example.com
password: adminpassword
```

<!-- :iphone: -->
## 📱 API Reference <a name = "api"></a>
This project includes an API that can be used with a Mobile app. 

To access the Postman collection and API documentation, navigate to the `HekaytiApiDocumentationAndCollection` folder:

- [Hekayti-Api-Documentation.xlsx](HekaytiApiDocumentationAndCollection/Hekayti-Api-Documentation.xlsx)
- [Hekayti-Api.postman_collection.json](HekaytiApiDocumentationAndCollection/Hekayti-Api.postman_collection.json)

<!-- :warning: -->
## ⚠️ Troubleshooting <a name = "troubleshooting"></a>
<p>If you encounter any issues during the setup process, refer to the <a href="https://laravel.com/docs" target="_new">Laravel documentation</a> or search for solutions on <a href="https://stackoverflow.com/" target="_new">Stack Overflow</a>.</p>

<!-- :raised_hands: -->
## 🙌 Contributing <a name = "contributing"></a>
If you'd like to contribute to the project, feel free to submit pull requests.

<!-- :scroll: -->
## 📜 License <a name = "license"></a>
<p>This project is licensed under the <a href="https://github.com/MohammedShwabi/hekayti-laravel/blob/main/LICENSE.md">MIT License</a>.</p>

## ✍️ Authors <a name = "authors"></a>

- [@MohammedShwabi](https://github.com/MohammedShwabi) Backend Development
- [@HeshamNoaman](https://github.com/HeshamNoaman) Frontend Development

See also the list of [contributors](https://github.com/MohammedShwabi/hekayti-laravel/contributors) who participated in this project.

## 🎉 Acknowledgements <a name = "acknowledgement"></a>

- [@MaryamHajeb](https://github.com/MaryamHajeb) for analysis, database design, and story creation.
- [@almomyz](https://github.com/almomyz) for contributing ideas, dedication in the work, and developing the mobile app.
- [@osama-nasser1999](https://github.com/osama-nasser1999) for contributing ideas and participating in the development of the mobile app.

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
<!-- small icon -->
[status-shield]: https://img.shields.io/badge/status-active-success.svg

[pull-shield]: https://img.shields.io/github/issues-pr/kylelobo/The-Documentation-Compendium.svg
[pull-url]: https://github.com/MohammedShwabi/hekayti-laravel/issues/pulls

[license-shield]: https://img.shields.io/badge/license-MIT-blue.svg
[license-url]: https://github.com/MohammedShwabi/hekayti-laravel/blob/main/LICENSE.md

<!-- built using icons -->
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[JQuery.com]: https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white
[JQuery-url]: https://jquery.com 
[MySQL.com]: https://img.shields.io/badge/mysql-4479A1?style=for-the-badge&logo=mysql&logoColor=white
[MySQL-url]: https://mysql.com/

<!-- image -->
[login-screenshot]: /screenshot/screenshot.jpeg
[story-screenshot]: /screenshot/screenshot1.png
[slide-screenshot]: /screenshot/screenshot2.jpeg
[admin-screenshot]: /screenshot/screenshot3.png
[home-screenshot]: /screenshot/screenshot4.jpeg
[profile-screenshot]: /screenshot/screenshot5.jpeg
