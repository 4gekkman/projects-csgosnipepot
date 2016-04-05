//------------------------------------------//
//                                          //
//          GulpFile.js DLW-пакета					//
//                                          //
//------------------------------------------//
//	Оглавление  //
//--------------//
/*

	1. Подключить необходимые NPM-пакеты
	2. Создать файл с датой последнего исполнения рядом с gulpfile.js
	3. Обработать файл Public/css/c.scss
	4. Обработать файлы в каталоге Public/js
	5. Обработать каталог Public/assets

	x. Выполнить все необходимые задачи этого gulpfile
	y. Функционал для инкрементальной вёрстки с realtime-обновлением документа
	n. Примеры часто используемых задач

		▪ Обработать SCSS-исходники
		▪ Скопировать всё из assets в public
		▪ Параллельно выполнить задачи styles и assets

*/
//------------------------------------------//
//	Код  //
//-------//

// 1. Подключить необходимые NPM-пакеты
'use strict';
const gulp = require('gulp');
const sass = require('../R5/node_modules/gulp-sass');
const file = require('../R5/node_modules/gulp-file');
const cssnano = require('../R5/node_modules/gulp-cssnano');
const uglify = require('../R5/node_modules/gulp-uglify');
const concat = require('../R5/node_modules/gulp-concat');
const fs = require('fs');
const sourcemaps = require('../R5/node_modules/gulp-sourcemaps');

// 2. Создать файл с датой последнего исполнения рядом с gulpfile.js
gulp.task('lastuse', function(callback) {

	fs.writeFile("lastuse", "Дата и время (UTC) последнего выполнения gulp-задачи для этого DLW-пакета.\n"+new Date().toUTCString());
  callback();

});

// 3. Обработать файл Public/css/c.scss
gulp.task('styles', function(callback){

	return gulp.src('Public/css/c.scss')
			.pipe(sourcemaps.init())
			.pipe(sass())
			.pipe(cssnano())
			.pipe(sourcemaps.write())
			.pipe(gulp.dest('../../../public/public/D1/css'));

});

// 4. Обработать файлы в каталоге Public/js
gulp.task('javascript', function(callback){

	return gulp.src(['Public/js/m.js', 'Public/js/f.js', 'Public/js/j.js'])
			.pipe(sourcemaps.init())
			.pipe(concat('j.js'))
			.pipe(uglify())
			.pipe(sourcemaps.write())
			.pipe(gulp.dest('../../../public/public/D1/js'));

});

// 5. Обработать каталог Public/assets
gulp.task('assets', function(){
	return gulp.src('Public/assets/**', {since: gulp.lastRun('assets')})
			.pipe(gulp.dest('../../../public/public/D1/assets'));
});




// x. Выполнить все необходимые задачи этого gulpfile
gulp.task('run', gulp.series(
	gulp.parallel('lastuse', 'styles', 'javascript', 'assets')
));

// y. Функционал для инкрементальной вёрстки с realtime-обновлением документа

	// y.1. Подготовить массив путей к каталогам с фронтенд-исходниками
	// - Для этого D-пакета, а также всех LW-пакетов, от которых он зависит
	var sources = [];
	sources['styles'] = [];
	sources['javascript'] = [];
	sources['assets'] = [];

		// sources: start
		sources['styles'] = [

		];
		sources['javascript'] = [

		];
		sources['assets'] = [

		];
		// sources: end

	// y.2. Подготовить массив путей к каталогам с фронтенд-результатами
	// - Для этого D-пакета, а также всех LW-пакетов, от которых он зависит
	var dests = [];
	dests['styles'] = [];
	dests['javascript'] = [];
	dests['assets'] = [];

		// dests: start
		dests['styles'] = [

		];
		dests['javascript'] = [

		];
		dests['assets'] = [

		];
		// dests: end

	// y.3. Следить за файлами в sources, запускать задачу при их изменении
	gulp.task('watch', function(){

		// styles
		for(var i=0; i<sources['styles'].length; i++) {
			gulp.watch(sources['styles'][i], {usePolling: true}, gulp.series('styles'));
		}

		// javascript
		for(var i=0; i<sources['javascript'].length; i++) {
			gulp.watch(sources['javascript'][i], {usePolling: true}, gulp.series('javascript'));
		}

		// assets
		for(var i=0; i<sources['assets'].length; i++) {
			gulp.watch(sources['assets'][i], {usePolling: true}, gulp.series('assets'));
		}

	});

	// y.4. Настройка browser-sync
	// - Запустить мини-сервер для отладки blade-документа этого D-пакета (либо можно использовать прокси)
	// - Следить за файлами в dests, перезагружать документ при их изменении
	gulp.task('serve', function(){

		// y.4.1] Запустить proxy
		browserSync.init({
			server: "public",
			port: 3000,
			ui: {
				port: 3001
			}
		});

		// y.4.2] Отслеживать изменения в указанных файлах

			// styles
			for(var i=0; i<dests['styles'].length; i++) {
				browserSync.watch(dests['styles'][i], {usePolling: true}).on('change', browserSync.reload);
			}

			// javascript
			for(var i=0; i<dests['javascript'].length; i++) {
				browserSync.watch(dests['javascript'][i], {usePolling: true}).on('change', browserSync.reload);
			}

			// assets
			for(var i=0; i<dests['assets'].length; i++) {
				browserSync.watch(dests['assets'][i], {usePolling: true}).on('change', browserSync.reload);
			}

	});

	// y.5. Задача для запуска watch и serve параллельно
	gulp.task('dev',
			gulp.series('run', gulp.parallel('watch', 'serve')));

// n. Примеры часто используемых задач


	// Обработать SCSS-исходники
	// gulp.task('styles', function(callback){

	// 	// Найти и обработать .scss файлы, записать в public
	// 	return gulp.src('frontend/styles/main.scss')
	// 			.pipe(sourcemaps.init())
	// 			.pipe(sass())
	// 			.pipe(sourcemaps.write())
	// 			.pipe(gulp.dest('public'));

	// 	// Сигнализировать о завершении задачи
	// 	//callback();

	// });


	// Скопировать всё из assets в public
	// gulp.task('assets', function(){
	// 	return gulp.src('frontend/assets/**', {since: gulp.lastRun('assets')})
	// 			.pipe(gulp.dest('public'));
	// });


	// Параллельно выполнить задачи styles и assets
	// gulp.task('build', gulp.series(
	// 	gulp.parallel('styles', 'assets')
	// ));


