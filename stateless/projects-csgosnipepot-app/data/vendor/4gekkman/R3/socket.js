////=============================================////
////                                             ////
////            Nodejs websocket сервер          ////
////																			       ////
////=============================================////

	// 1. Подключить модули http и socket.io
	var app = require('http').createServer(handler);
	var io = require('socket.io')(app);


	// 2. Подключить модуль ioredis
	// - И создать новое подключение к Redis
	var Redis = require('ioredis');
	var redis = new Redis();


	// 3. Слушать HTTP-порт 6001
	// - По нему будут подключаться клиенты
	app.listen(6001, function() {
		console.log('Server is running!');
	});


	// 4. Собственно, код http-сервера nodejs
	// - Он лишь возвращает в ответе код 200 (ОК).
	function handler(req, res) {
		res.writeHead(200);
		res.end('');
	}


	// 5. Назначить функцию-обработчик
	// - Будет срабатывать каждый раз при подключении нового клиента к серверу
	// - А точнее, при создании нового соединения по протоколу websocket.
	io.on('connection', function(socket) {
		//
	});


	// 6. Слушать все каналы Redis'а (паттерн pub/sub)
	redis.psubscribe('*', function(err, count) {
		//
	});


	// 7. Назначить функцию-обработчик
	// - Срабатывает, когда в любой канал Redis'а поступает новое сообщение
	redis.on('pmessage', function(subscribed, channel, message) {

		// Распарсить message
		// - message.event  | содержит имя события в Laravel, квалифицированное полным пр.имён
		// - message.data   | содержит данные, которые надо отправить клиентам
		message = JSON.parse(message);

		// Отправить всем подписанным на канал клиентам данные
		// - Имя канала: channel + ':' + message.event
		io.emit(channel, message.data);

	});