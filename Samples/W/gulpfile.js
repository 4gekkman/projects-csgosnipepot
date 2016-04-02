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
	3. ...

	x. Выполнить все необходимые задачи этого gulpfile
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
const gulp = require('../R5/node_modules/gulp');
const sass = require('../R5/node_modules/gulp-sass');
const file = require('../R5/node_modules/gulp-file');
const fs = require('fs');
const sourcemaps = require('../R5/node_modules/gulp-sourcemaps');

// 2. Создать файл с датой последнего исполнения рядом с gulpfile.js
gulp.task('lastuse', function(callback) {

	fs.writeFile("lastuse", "Дата и время (UTC) последнего выполнения gulp-задачи для этого DLW-пакета.\n"+new Date().toUTCString());
  callback();

});

// 3. 


// x. Выполнить все необходимые задачи этого gulpfile
gulp.task('run', gulp.series(
	gulp.parallel('lastuse')
));


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


