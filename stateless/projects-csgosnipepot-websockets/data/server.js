////=============================================////
////                                             ////
////            Nodejs websocket сервер          ////
////																			       ////
////=============================================////

	// 1. Подключить модули http и socket.io
	const app 				= require('http').createServer(handler);
	const io 					= require('socket.io')(app);
	const log 				= require('winston');
	const CryptoJS 		= require("crypto-js");
  const co 					= require('co');
  const utf82hex 		= require('utf82hex');
	const Serialize 	= require('php-serialize');
  const isNumeric 	= require('is-numeric');
  const numberIsNan = require('number-is-nan');
  const Redis       = require('ioredis');

	// 2. Подключить модуль ioredis
	// - И создать новое подключение к Redis
	var redis = new Redis({
		host: 'redis',
		port: '6379',
    password: 'Whn8VKkzaFjJDJY9QDZXUr7Bk29GdYNBpWJatWyqyMPqnrBgfzCxFWUAZxJ7wPycxTxj8xTc9cd7LGGBZeZUTc8GuNF544hLZpx73qjjg635Xq89yNGr7xNGaqPBKcgyKBfMJMYDADxXNP9m5Rt7RxTZzG5P4vBBkDyEjgDBBaXn6FPLsZUXBVkRMSX8BDEwR63cPNt4mMkwfrp2EU2JxQKWH5ABjNtKALmzKJj8j8EBQmJBjnXJQ8HZ7Uq6pz9P'
	});

	// 3. Слушать HTTP-порт 6001
	// - По нему будут подключаться клиенты
	app.listen(6001, function() {
		//console.log('Server is running!');
	});

	// 4. Собственно, код http-сервера nodejs
	// - Он лишь возвращает в ответе код 200 (ОК).
	function handler(req, res) {
		res.writeHead(200);
		res.end('');
	}

	// 5. Подготовить счётчик подключенных пользователей
	var countusers = 0;

	// 6. Назначить обработчик для работы с подключением
	io.on('connection', function(socket) {

		// 6.a. Вкл/Выкл режим отладки (логи)
		var is_debug_on = false;

		// 6.1. Создать подключение к redis
		var redis = new Redis({
			host: 'redis',
			port: '6379',
      password: 'Whn8VKkzaFjJDJY9QDZXUr7Bk29GdYNBpWJatWyqyMPqnrBgfzCxFWUAZxJ7wPycxTxj8xTc9cd7LGGBZeZUTc8GuNF544hLZpx73qjjg635Xq89yNGr7xNGaqPBKcgyKBfMJMYDADxXNP9m5Rt7RxTZzG5P4vBBkDyEjgDBBaXn6FPLsZUXBVkRMSX8BDEwR63cPNt4mMkwfrp2EU2JxQKWH5ABjNtKALmzKJj8j8EBQmJBjnXJQ8HZ7Uq6pz9P'
		});

		// 6.2. Получить куку about_user из handshake.query
		var auth = (function(){

			var result = socket.handshake.query.about_user;
			if(!result) result = "";
			return result;

		})();

		// 6.3. Получить куку prefix из handshake.query
		var prefix = (function(){

			var result = socket.handshake.query.prefix;
			if(!result) result = "";
			return result;

		})();

		// 6.4. Получить key и cipher приложения из Redis
		co(function*() {

			let key 		= yield redis.get('m1:app:key');
			let cipher	= yield redis.get('m1:app:cipher');
			return {
				key: 		key,
				cipher: cipher
			};

		}).then(function(key_and_cipher){

			// 1] Получить ключ приложения
			let key = CryptoJS.enc.Utf8.parse(key_and_cipher.key);

			// 2] Получить алгоритм шифрования приложения
			let cipher = key_and_cipher.cipher;

			// 3] Получить расшифрованный auth
			let auth_decrypted = (function(){

				// 3.1] Если auth пуст, вернуть ""
				if(!auth || !key_and_cipher.key || !key_and_cipher.cipher) return "";

				// 3.2] Расшифровать auth из base64
				let base64_decrypted = CryptoJS.enc.Base64.parse(auth).toString(CryptoJS.enc.Utf8);
				if(!base64_decrypted) return "";

				// 3.3] Десериализовать base64_decrypted в объект
				let json = JSON.parse(base64_decrypted);
				if(!json) return "";

				// 3.4] Получить iv и value
				if(!json.value || !json.iv)
					return "";
				let iv 		= CryptoJS.enc.Base64.parse(json.iv); //Buffer.from(json.iv, 'base64').toString();
				let value = json.value;

				// 3.5] Получить расшифрованное значение value
				let value_decrypted = CryptoJS.AES.decrypt(value, key, {
					iv: iv
				});

				// 3.6] Получить UTF-8 значние для value_decrypted
				let value_decrypted_utf8 = value_decrypted.toString(CryptoJS.enc.Utf8);

				// 3.7] Получить десериализованное значение для value_decrypted_utf8
				let auth_json = Serialize.unserialize(value_decrypted_utf8);

				// 3.8] Получить auth в виде JS-объекта
				let auth_result = JSON.parse(auth_json);

				// 3.n] Вернуть результат
				return auth_result;

			})();

			// 4] Если auth_decrypted пуст, завершить
			if(!auth_decrypted) return;

			// 5] Извлечь из auth_decrypted значения is_anon и id
			let is_anon = auth_decrypted.is_anon;
			var id_user = auth_decrypted.id_user;

			// 6] Если это анонимный пользователь, или значения не найдены, завершить
			if(is_anon == 1 || (!is_anon && is_anon !== 0 && is_anon !== "0") || !id_user)
				return;

			// 7] Запросить из redis значение метки онлайна для пользователя id_user
			// - Если метки нет, то создать её со значением 1.
			// - Если метка есть, то прибавить к её значению 1, и перезаписать.
			co(function*() {

				var mark 		= yield redis.get("m16:online:mark:"+id_user);
				return mark;

			}).then(function(mark){

				if(is_debug_on) log.info(mark);
				if(is_debug_on) log.info(mark+"");
				if(is_debug_on) log.info(!mark);
				if(is_debug_on) log.info(isNumeric(mark));
				if(is_debug_on) log.info(numberIsNan(mark));

				// 7.1] Если метки нет, то создать её со значением 1.
				if(!mark) {
					if(is_debug_on) log.info('Инициация mark значением 1');
					redis.set("m16:online:mark:"+id_user, 1);
				}

				// 7.2] Если метка есть, то прибавить к её значению 1, и перезаписать.
				else {
					if(is_debug_on) log.info('Прибавление к mark значения 1');
					if(is_debug_on) log.info('Старое значение mark = '+mark);
					mark = +mark + 1;
					if(is_debug_on) log.info('Новое значение mark = '+mark);
					if(!numberIsNan(mark) && isNumeric(mark))
						redis.set("m16:online:mark:"+id_user, mark);
				}

			});

			// 8] Записать id_user в socket, если это не анонимный пользователь
			if(is_anon == 0)
				socket['m16:online:id_user'] = id_user;

		});

		// 6.5. Прибавить к countusers единицу
		countusers++;

		// 6.6. Сообщить всем клиентам, что подключился новый пользователь

			// Записать актуальное кол-во подключений в redis
			redis.set('active_connections_number', countusers);

			// Сообщить актуальное кол-во подключений
			io.emit('active_connections_number', countusers);

		// 6.7. Назначить обработчик события разрыва соединения
		socket.once('disconnect', function () {

			// 6.7.1. Отнять от countusers единицу
			countusers--;

			// 6.7.2. Записать актуальное кол-во подключений в redis
			redis.set('active_connections_number', countusers);

			// 6.7.3. Сообщить актуальное кол-во подключений
			io.emit('active_connections_number', countusers);

			// 6.7.4. Уменьшить значение mark на 1, или удалить его для пользователя socket['m16:online:id_user']

				// 1] Получить ID пользователя
				var id_user = socket['m16:online:id_user'];

				// 2] Запросить из redis значение метки онлайна для пользователя id_user
				// - Если метки нет, то ничего не делать.
				// - Если метка есть, то:
				// 	 - Если это не число, удалить её.
				// 	 - Если это число, получить значение и уменьшить на 1.
				// 	 	 - Если после этого значение станет <= 0, удалить её.
				// 	 	 - В ином случае, перезаписать в redis.
				co(function*() {

					var mark 		= yield redis.get("m16:online:mark:"+id_user);
					return mark;

				}).then(function(mark){

					// 7.1] Если метки нет, ничего не делать
					if(!mark)
						return;

					// 7.2] Если метка есть
					else {

						// 7.2.1] Если метка не число, удалить её.
						if(!isNumeric(mark)) {
							redis.pipeline().del("m16:online:mark:"+id_user).exec(function (err, results) {});
						}

						// 7.2.2] Если метка число
						else {

							// 1) Уменьшить mark на 1
							mark = +mark - 1;
							if(is_debug_on) log.info('mark = '+mark);

							// 2) Если mark <= 0, удалить метку
							if(mark <= 0) {
								if(is_debug_on) log.info('Удаляю метку пользователя №'+id_user);
								redis.pipeline().del("m16:online:mark:"+id_user).exec(function (err, results) {});
							}

							// 3) Если mark > 0, перезаписать метку
							else {
								if(is_debug_on) log.info('Уменьшаю метку пользователя №'+id_user+' на 1. Теперь она равна: '+mark);
								redis.pipeline().set("m16:online:mark:"+id_user, mark).exec(function (err, results) {});
							}

						}

					}
          
          // 7.3] Удалить подключение к Redis
          redis.disconnect();

				});
        
        

		});

	});

	// 6. Слушать все каналы Redis'а (паттерн pub/sub)
	redis.psubscribe('*', function(err, count) {
		//console.log('New message in some channel!');
	});

	// 7. Назначить функцию-обработчик
	// - Срабатывает, когда в любой канал Redis'а поступает новое сообщение
	redis.on('pmessage', function(subscribed, channel, message) {

		// Сообщить о том, что обработчик сработал
		//console.log('Handler is working! Channel: '+channel+'. Message: '+message);	

    // Добавить message в лог
    //log.info(message);
    
		// Распарсить message
		// - message.event  | содержит имя события в Laravel, квалифицированное полным пр.имён
		// - message.data   | содержит данные, которые надо отправить клиентам
		message = JSON.parse(message);

		// Отправить всем подписанным на канал клиентам данные
		io.emit(channel, message.data);

	});