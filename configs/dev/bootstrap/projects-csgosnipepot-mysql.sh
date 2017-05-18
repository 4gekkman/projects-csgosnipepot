#!/bin/bash

  # 1. Скопировать my_cnf_original в my.cnf
  cp -f /etc/mysql/conf.d/my_cnf_original /etc/mysql/conf.d/my.cnf

  # 2. Изменить права MySQL-конфига my.cnf на 644
  chmode 644 /etc/mysql/conf.d/my.cnf
  
  # n. Завершить скрипт
  exit 0