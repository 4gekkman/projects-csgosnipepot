<?php/* =================================================////
////																										 ////
////                     Шаблон L-пакета				         ////
////																										 ////
////=====================================================////
//// 			        		 		   ////
//// 			  Оглавление			 ////
//// 			         				   ////
////=========================////


    А. Содержимое HEAD html-разметки шаблона
    ----------------------------------------
      А1. Подключение тэга title с названием документа
      А2. Подключение локального CSS документа

    Б. Содержимое BODY html-разметки шаблона
    ----------------------------------------
      Б1. Блок wrapper (главный) шаблона AdminLTE
        Б1.1. Header
          Б1.1.1. Логотип
          Б1.1.2. Навигационная полоса
            1] Кнопка раскрытия/сокрытия левого сайдбара
            2] Навигационное меню в правой части верхней нав.полосы
        Б1.2. Левый столбец (содержит левый сайдбар)
          Б1.2.1. Левый сайдбар
            1] Меню левого сайдбара
        Б1.3. Заголовок, хлебные крошки, контент документа
        Б1.4. Footer
          Б1.4.1. Правая часть футера
          Б1.4.2. Левая часть футера
        Б1.5. Правый столбец
          Б1.5.1. Табы
          Б1.5.2. Панели табов

    В. Подключение ресурсов шаблона
    -------------------------------
      В1. Принять данные для шаблона с сервера
      В2. Подключение JS-скрипта наследника шаблона


////==================================================== */?>

<!doctype html>
<html lang="ru">


<?php /*----------------------------------------->
<!-- А. Содержимое HEAD html-разметки шаблона  -->
<!-------------------------------------------*/ ?>
<head>
  <meta charset="UTF-8">

  <?php /*-- А1. Подключение тэга title с названием документа -->
  <!--------------------------------------------------------*/ ?>
  @yield('title')

  <?php /*-- А2. Подключение локального CSS документа -->
  <!------------------------------------------------*/ ?>
  @yield('css')

</head>

<?php /*----------------------------------------->
<!-- Б. Содержимое BODY html-разметки шаблона  -->
<!-------------------------------------------*/ ?>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="skin-green sidebar-mini">

  <!------------------------------------------------->
  <!-- Б1. Блок wrapper (главный) шаблона AdminLTE -->
  <!------------------------------------------------->
  <div class="wrapper" id="layoutmodel">

    <!------------------>
    <!-- Б1.1. Header -->
    <!------------------>
    <header class="main-header">

      <!-- Б1.1.1. Логотип -->
      <!--------------------->
      <a class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>L</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>L</b>ogo</span>
      </a>

      <!-- Б1.1.2. Верхняя навигационная полоса -->
      <!------------------------------------------>
      <nav class="navbar navbar-static-top" role="navigation">

        <!-- 1] Кнопка раскрытия/сокрытия левого сайдбара -->
        <!-------------------------------------------------->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>

        <!-- 2] Навигационное меню в правой части верхней нав.полосы -->
        <!------------------------------------------------------------->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                <img src="http://placehold.it/100x100" class="user-image" alt="User Image">  <!-- {!! asset('public/L10000/assets/Photo.png') !!} -->
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs" data-bind="text: m.s1.fio"></span>
              </a>
              <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header">
                  <img src="http://placehold.it/100x100" class="img-circle" alt="User Image">

                  <p>
                    <span data-bind="text: m.s1.fio"></span>
                    <small data-bind="text: m.s0.auth.user().email"></small>
                    <small data-bind="text: m.s0.auth.user().phone"></small>
                  </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{!! asset('dashboard/profile') !!}" class="btn btn-default btn-flat">Profile</a>
                  </div>
                  <div class="pull-right">
                    <a href="#" class="btn btn-default btn-flat" data-bind="click: f.s1.logout">Logout</a>
                  </div>
                </li>
              </ul>
            </li>
            <!-- Control Sidebar Toggle Button -->
            <li>
              <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
            </li>

          </ul>
        </div>

      </nav>

    </header>

    <!-------------------------------------------------->
    <!-- Б1.2. Левый столбец (содержит левый сайдбар) -->
    <!-------------------------------------------------->
    <aside class="main-sidebar">

      <!-- Б1.2.1. Левый сайдбар -->
      <!--------------------------->
      <section class="sidebar">

        <!-- 1] Меню левого сайдбара -->
        <!----------------------------->
        <ul class="sidebar-menu">

          <!-- 1.1] Группа "NAVIGATION -->
          <!----------------------------->
          <li class="header">NAVIGATION</li>

            <!-- # Demonstration -->
            <li class=""><a href="{!! asset('dashboard/demonstration') !!}"><i class="fa fa-dashboard"></i> <span>Demonstration</span></a></li>

            <!-- # Users control system -->
            <li class=""><a href="{!! asset('dashboard/users') !!}"><i class="fa fa-users"></i> <span>Users control system</span></a></li>

            <!-- # Images upload system -->
            <li class=""><a href="{!! asset('dashboard/images') !!}"><i class="fa fa-image"></i> <span>Images upload system</span></a></li>


          <!-- 1.3] Группа "USER PROFILE -->
          <!------------------------------->
          <li class="header">USER PROFILE</li>
          <li>
            <div  class="user-panel">
              <div class="pull-left image">
                <img src="http://placehold.it/100x100" class="img-circle" alt="User Image">
              </div>
              <div class="pull-left info infotext">
                <p data-bind="text: m.s1.fio"></p>
              </div>
            </div>
          </li>
          <li><a href="{!! asset('dashboard/profile') !!}"><i class="fa fa-bars text-aqua"></i> <span>Profile</span></a></li>
          <li data-bind="click: f.s1.logout"><a href="#"><i class="fa fa-ban text-red"></i><span>Logout</span></a></li>

        </ul>

      </section>

    </aside>

    <!-------------------------------------------------------->
    <!-- Б1.3. Заголовок, хлебные крошки, контент документа -->
    <!-------------------------------------------------------->
    <div class="content-wrapper" style="min-height: 300px;" data-bind="stopBindings: true">
      <section id="content" class="content">

        @yield('content')

      </section>
    </div>
    <!-- /.content-wrapper -->

    <!------------------>
    <!-- Б1.4. Footer -->
    <!------------------>
    <footer class="main-footer">

      <!-- Б1.4.1. Правая часть футера -->
      <!--------------------------------->
      <div class="pull-right hidden-xs">
        Dashboard
      </div>

      <!-- Б1.4.2. Левая часть футера -->
      <!-------------------------------->
      <strong>Copyright © 2016 <a href="#">Company</a>.</strong> All rights reserved.

    </footer>

    <!-------------------------->
    <!-- Б1.5. Правый столбец -->
    <!-------------------------->
    <aside class="control-sidebar control-sidebar-dark">

      <!-- Б1.5.1. Табы -->
      <!------------------>
      <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
      </ul>

      <!-- Б1.5.2. Панели табов -->
      <!-------------------------->
      <div class="tab-content">

        <!-- 1] Таб "home" -->
        <!------------------->
        <div class="tab-pane active" id="control-sidebar-home-tab">
          <h3 class="control-sidebar-heading">Recent Activity</h3>
          <ul class="control-sidebar-menu">
            <li>
              <a href="javascript::;">
                <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                <div class="menu-info">
                  <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                  <p>Will be 23 on April 24th</p>
                </div>
              </a>
            </li>
          </ul>

          <h3 class="control-sidebar-heading">Tasks Progress</h3>
          <ul class="control-sidebar-menu">
            <li>
              <a href="javascript::;">
                <h4 class="control-sidebar-subheading">
                  Custom Template Design
                  <span class="label label-danger pull-right">70%</span>
                </h4>

                <div class="progress progress-xxs">
                  <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                </div>
              </a>
            </li>
          </ul>

        </div>

        <!-- 2] Таб "settings" -->
        <!----------------------->
        <div class="tab-pane" id="control-sidebar-settings-tab">
          <form method="post">
            <h3 class="control-sidebar-heading">General Settings</h3>

            <div class="form-group">
              <label class="control-sidebar-subheading">
                Report panel usage
                <input type="checkbox" class="pull-right" checked="">
              </label>

              <p>
                Some information about this general settings option
              </p>
            </div>
            <!-- /.form-group -->
          </form>
        </div>

      </div>

    </aside>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg" style="position: fixed; height: auto;"></div>
  </div>

</body>
</html>


<?php /*-------------------------------->
<!-- В. Подключение ресурсов шаблона  -->
<!----------------------------------*/ ?>

  <?php /*----------------------------------------->
  <!-- В1. Принять данные для шаблона с сервера  -->
  <!-------------------------------------------*/ ?>
  <script>

    // 1. Подготовить объект для JS-кода шаблона
    window["layout_PARAMpackfullnamePARAM"] = {};

    // 2. Принять данные для шаблона

      window["layout_PARAMpackfullnamePARAM"].csrf_token  = "{{ csrf_token() }}";

  </script>

  <?php /*---------------------------------------------->
  <!-- В2. Подключение JS-скрипта наследника шаблона  -->
  <!------------------------------------------------*/ ?>

    <?php /* JS наследника шаблона */ ?>
    @yield('js')








