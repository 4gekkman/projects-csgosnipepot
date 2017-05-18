#!/bin/bash

  # 1. Скопировать resolv с правильным IP DNS-сервера для postfix
  cp -f /etc/resolv.conf /var/spool/postfix/etc/resolv.conf

  # 2. Скопировать /etc/logrotate.d/rotate_project_logs_original в /etc/logrotate.d/rotate_project_logs
  # - И заменить права на 644.
  cp /etc/logrotate.d/rotate_project_logs_original /etc/logrotate.d/rotate_project_logs
  chmod 644 /etc/logrotate.d/rotate_project_logs  
  
  # 3. Записать файл /etc/cron.d/laravel
  # - Чтобы писать результаты в лог, надо добавлять в конце каждой команды: >> /var/log/cron.log 2>&1
  # - А чтобы никуда ничего не писать, надо добавлять в конце каждой команды: >> /dev/null
  rm -f /etc/cron.d/laravel
  echo "* * * * * root php /theproject/service/artisan schedule:run >> /dev/null" > /etc/cron.d/laravel
  echo "* * * * * root /usr/sbin/logrotate -v /etc/logrotate.d/rotate_project_logs >> /dev/null" >> /etc/cron.d/laravel
  chmod 644 /etc/cron.d/laravel

  # 4. Запустить тики в проекте
  /theproject/service/artisan m11:start
  
  # 5. Прибить демона queue:work, обрабатывающего процессинг игры "Лотерея", и выплату выигрышей
  # - После этого supervisord автоматически его перезапустит, и он будет работать.
  # - Сделать это через 5 секунд после запуска.
  timeout --signal=SIGINT 5 kill $(ps aux | grep queue:work | grep processor_main | grep processor_hard | grep -v grep | awk '{print $2}')

  # 6. Добавить в SSH-агент приватный SSH-ключ доступа к github для команды m1:clone
  #eval `ssh-agent -s`
  #cp /root/.ssh/keys_from_server/id_rsa4clone /root/.ssh/id_rsa4clone   # Скопировать ключ, переданный с машины-хозяина, чтобы можно было изменить его права
  #chmod 400 /root/.ssh/id_rsa4clone                                     # Уменьшить права, иначе не добавится
  #ssh-add /root/.ssh/id_rsa4clone                                       # Ключ добавляем в .bashrc
  #ssh-keyscan github.com >> /root/.ssh/known_hosts                      # Добавляем github.com в known_hosts, чтобы при каждом m1:clone не спрашивало
  
  # 7. Сделать, чтобы logrotate срабатывал раз в минуту
  rm -f /etc/cron.d/logrotate
  echo "* * * * * root bash /etc/logrotate.d/rotate_project_logs >> /dev/null" > /etc/cron.d/logrotate
  chmod 644 /etc/cron.d/logrotate
  
  # n. Завершить скрипт
  exit 0