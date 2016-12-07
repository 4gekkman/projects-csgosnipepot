<?php
////======================================================////
////																										  ////
////                   Событие Broadcast       			      ////
////																											////
////======================================================////

  //-------------------//
  // Пространство имён //
  //-------------------//

    namespace R2;

  //---------------------------------//
  // Подключение необходимых классов //
  //---------------------------------//

    // Базовые классы, необходимые для работы событий вообще
    use App\Events\Event as OriginalEvent,
        Illuminate\Queue\SerializesModels,
        Illuminate\Broadcasting\Channel,
        Illuminate\Broadcasting\PrivateChannel,
        Illuminate\Broadcasting\PresenceChannel,
        Illuminate\Broadcasting\InteractsWithSockets,
        Illuminate\Contracts\Broadcasting\ShouldBroadcast;

  //---------------//
  // Событие Event //
  //---------------//
  class Broadcast extends OriginalEvent implements ShouldBroadcast
  {

      //---------------------//
      // А. Подключить трейт //
      //---------------------//
      use SerializesModels;

      //-------------------------------------//
      // Б. Переменные для приёма аргументов //
      //-------------------------------------//
      // - При трансляции, public-переменные будут переданы
      public $data;

      //------------------------------------------------------------------//
      // В. Принять аргументы, переданные при создании экземпляра события //
      //------------------------------------------------------------------//
      // - Переданные в аргументе экземпляры модели будут сериализоваться
      public function __construct($data)
      {

        $this->data = $data;

      }

      //--------------------------------------//
      // Г. Методы для транслируемого события //
      //--------------------------------------//

        // Г1. Массив каналов, куда транслятор должен транслировать
        //---------------------------------------------------------
        public function broadcastOn()
        {

            $channels = $this->data['channels'];
            write2log($channels, []);
            //$channels_classes = [];
            //foreach($channels as $channel) {
            //  array_push($channels_classes, new Channel($channel));
            //}

            // Вернуть массив таких каналов
            // - Подсказка: можно использовать ID пользователя
            return $channels;

        }

        // Г2. Массив данных, которые пользователь должен транслировать
        //-------------------------------------------------------------
  //      public function broadcastWith()
  //      {
  //          return ['name' => 'Иван', 'age' => 18];
  //      }

        // Г3. В какую очередь поместить событие
        //--------------------------------------
        public function onQueue()
        {
          return array_key_exists('queue', $this->data) ? $this->data['queue'] : 'default';
        }


  }