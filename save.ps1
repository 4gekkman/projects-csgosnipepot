#########################################################################
##                                                                     ##
##   Powershell-скрипт автоматического сохранения проектов на github   ##
##                                                                     ##
######################################################################### 
##                ##
##   Оглавление   ##
##                ##
####################
#
#	a. Подготовка необходимого функционала
#	b. Подготовка SSH-agent'та к работе
# 
# 1. Сохранение projects на github
# 2. Сохранение моих laravel-пакетов на github
# 3. Сохранение docker-поддеревьев проекта на github
# 
# n. Перейти в окне терминала в первоначальный каталог
# 
######################################################################### 
# Сохранить путь к текущему каталогу в переменную #
$current_path = (Get-Location).Path


############################################
## a. Подготовка необходимого функционала ##
############################################

	##--------------------------------------##
	## a.1. Извлечь PID текущего SSH-agent  ##
	##--------------------------------------##
	## - Если SSH-agent не запущен, вернёт 0.
	## - Можно использовать, чтобы проверить, запущен ли уже SSH-агент.

	  function Get-SshAgent() {
		  $agentPid = $env:SSH_AGENT_PID

		  if ([int]$agentPid -eq 0) {
			  0
		  } else {
			  # Make sure the process is actually running
			  $process = Get-Process -Id $agentPid -ErrorAction SilentlyContinue

			  if(($process -eq $null) -or ($process.ProcessName -ne "ssh-agent")) {
				  # It is not running (this is an error). Remove env vars and return 0 for no agent.
				  [Environment]::SetEnvironmentVariable("SSH_AGENT_PID", $null, "Process")
				  [Environment]::SetEnvironmentVariable("SSH_AGENT_PID", $null, "User")
				  [Environment]::SetEnvironmentVariable("SSH_AUTH_SOCK", $null, "Process")
				  [Environment]::SetEnvironmentVariable("SSH_AUTH_SOCK", $null, "User")
				  0
			  } else {
				  # It is running. Return the PID.
				  $agentPid
			  }
		  }
	  }

	##---------------------------##
	## a.2. Запустить SSH-agent  ##
	##---------------------------##
	## - После запуска публикует PID нового SSH-agent

	  function Start-SshAgent() {
		  # Start the agent and gather its feedback info
		  [string]$output = ssh-agent

		  $lines = $output.Split(";")
		  $agentPid = 0

		  foreach ($line in $lines) {
			  if (([string]$line).Trim() -match "(.+)=(.*)") {
				  # Set environment variables for user and current process.
				  [Environment]::SetEnvironmentVariable($matches[1], $matches[2], "Process")
				  [Environment]::SetEnvironmentVariable($matches[1], $matches[2], "User")

				  if ($matches[1] -eq "SSH_AGENT_PID") {
					  $agentPid = $matches[2]
				  }
			  }
		}

		  # Show the agent's PID as expected.
		  Write-Host "SSH agent PID:", $agentPid
	  }

	##---------------------------------------##
	## a.3. Остановить запущенный SSH-агент  ##
	##---------------------------------------##
	## -

	  function Stop-SshAgent() {
		  [int]$agentPid = Get-SshAgent
		  if ([int]$agentPid -gt 0) {
			  # Stop agent process
			  $proc = Get-Process -Id $agentPid
			  if ($proc -ne $null) {
				  Stop-Process $agentPid
			  }

			  # Remove all enviroment variables
			  [Environment]::SetEnvironmentVariable("SSH_AGENT_PID", $null, "Process")
			  [Environment]::SetEnvironmentVariable("SSH_AGENT_PID", $null, "User")
			  [Environment]::SetEnvironmentVariable("SSH_AUTH_SOCK", $null, "Process")
			  [Environment]::SetEnvironmentVariable("SSH_AUTH_SOCK", $null, "User")
		  }
	  }

	##-------------------------------------##
	## a.4. Добавить SSH-ключ в SSH-агент  ##
	##-------------------------------------##
	## - Путь к SSH-ключу передаётся в качестве аргумента

	  function Add-SshKey() {
		  if ($args.Count -eq 0) {
			  # Add the default key (./id_rsa)
			  ssh-add ".\id_rsa"
		  } else {
			  foreach ($value in $args) {
				  ssh-add $value
			  }
		  }
	  }

    
#########################################
## b. Подготовка SSH-agent'та к работе ##
#########################################

	# 1] Перейти в каталог, где лежит каталог .ssh
	cd "C:\WebDev\bin\ssh\Github - 4gekkman"
	
	# 2] Запустить SSH-agent
	Start-SshAgent
	
	# 3] Добавить SSH-ключ от github в SSH-агент
	Add-SshKey
  
  # 4] Вернуться в исходный каталог
  cd $current_path

  
######################################
## 1. Сохранение projects на github ##
#######################################################
# Из:      C:\WebDev\projects\<project>               #
# В:       git@github.com:4gekkman/<project>.git      #
# Триггер: каждые 3 часа                              #
####################################################### 

	########################
	## Подготовка функции ##
	########################	
	function PushProjectToGithub($project)
	{
		$msg = "Autosave"		
		git add -A --ignore-errors .
		git commit -m $msg
    git pull --commit $project master
		git push $project master
	}  
  
	#####################
	## Выполнение push ##
	#####################	  
  PushProjectToGithub "csgosnipepot"

  
##################################################
## 2. Сохранение моих laravel-пакетов на github ##
##################################################
	
	# 2.1. Подготовка функции для залива свежих данных на github
	function PushPacksSubtreesToGithub($prefix, $github)
	{
		$msg = "Autosave"		
		git add -A --ignore-errors .
		git commit -m $msg
    git subtree pull --prefix=$prefix $github master
    git subtree push --squash --prefix=$prefix $github master  
	}	
  
  # 2.2. Подготовка списка пакетов, данные по которым надо залить 
  $laravelpacks = '[
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10000",
        "github": "git@github.com:4gekkman/D10000.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10003",
        "github": "git@github.com:4gekkman/D10003.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10004",
        "github": "git@github.com:4gekkman/D10004.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10005",
        "github": "git@github.com:4gekkman/D10005.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10006",
        "github": "git@github.com:4gekkman/D10006.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10009",
        "github": "git@github.com:4gekkman/D10009.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10010",
        "github": "git@github.com:4gekkman/D10010.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10011",
        "github": "git@github.com:4gekkman/D10011.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10012",
        "github": "git@github.com:4gekkman/D10012.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/D10013",
        "github": "git@github.com:4gekkman/D10013.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/L10000",
        "github": "git@github.com:4gekkman/L10000.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/L10001",
        "github": "git@github.com:4gekkman/L10001.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/L10003",
        "github": "git@github.com:4gekkman/L10003.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/L10004",
        "github": "git@github.com:4gekkman/L10004.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M1",
        "github": "git@github.com:4gekkman/M1.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M2",
        "github": "git@github.com:4gekkman/M2git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M3",
        "github": "git@github.com:4gekkman/M3.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M4",
        "github": "git@github.com:4gekkman/M4.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M5",
        "github": "git@github.com:4gekkman/M5.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M6",
        "github": "git@github.com:4gekkman/M6.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M7",
        "github": "git@github.com:4gekkman/M7.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M8",
        "github": "git@github.com:4gekkman/M8.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M9",
        "github": "git@github.com:4gekkman/M9.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M10",
        "github": "git@github.com:4gekkman/M10.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M11",
        "github": "git@github.com:4gekkman/M11.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M12",
        "github": "git@github.com:4gekkman/M12.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M13",
        "github": "git@github.com:4gekkman/M13.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M14",
        "github": "git@github.com:4gekkman/M14.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M15",
        "github": "git@github.com:4gekkman/M15.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M16",
        "github": "git@github.com:4gekkman/M16.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M17",
        "github": "git@github.com:4gekkman/M17.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/M18",
        "github": "git@github.com:4gekkman/M18.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/R1",
        "github": "git@github.com:4gekkman/R1.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/R2",
        "github": "git@github.com:4gekkman/R2.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/R3",
        "github": "git@github.com:4gekkman/R3.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/R4",
        "github": "git@github.com:4gekkman/R4.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/R5",
        "github": "git@github.com:4gekkman/R5.git"
    },
    {
        "prefix": "stateless/projects-csgosnipepot-app/data/vendor/4gekkman/R6",
        "github": "git@github.com:4gekkman/R6.git"
    }
]' | ConvertFrom-Json	
	
  # 2.3. Осуществление залива
  #foreach ($pack in $laravelpacks) { 
  #  PushPacksSubtreesToGithub $pack.prefix $pack.github
  #}
  
  
########################################################
## 3. Сохранение docker-поддеревьев проекта на github ##
########################################################  
  
	########################
	## Подготовка функции ##
	########################	
	function PushSubreeToGithub($prefix, $github)
	{
		$msg = "Autosave"		
		git add -A --ignore-errors .
		git commit -m $msg
    git subtree pull --prefix=$prefix $github master
    git subtree push --squash --prefix=$prefix $github master   
	}  
  
	#####################
	## Выполнение push ##
	#####################	  
  #PushSubreeToGithub "stateless/projects-csgosnipepot-app" "git@github.com:4gekkman/projects-csgosnipepot-app.git"
  #PushSubreeToGithub "stateless/projects-csgosnipepot-mysql/" "git@github.com:4gekkman/projects-csgosnipepot-mysql.git"
  #PushSubreeToGithub "stateless/projects-csgosnipepot-redis/" "git@github.com:4gekkman/projects-csgosnipepot-redis.git" 
  #PushSubreeToGithub "stateless/projects-csgosnipepot-websockets/" "git@github.com:4gekkman/projects-csgosnipepot-websockets.git"
  
  
##########################################################
## n. Перейти в окне терминала в первоначальный каталог ##
##########################################################
## - Который был до запуска этого скрипта.
cd $current_path
Stop-SshAgent
	