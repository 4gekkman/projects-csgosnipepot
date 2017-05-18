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
# 3. Сохранение поддеревьев проекта на github
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

	# Перейти в каталог, где лежит каталог .ssh
	cd "C:\WebDev\bin\ssh\Github - 4gekkman"
	
	# Запустить SSH-agent
	Start-SshAgent
	
	# Добавить SSH-ключ от github в SSH-агент
	Add-SshKey

  
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
	function PushProjectToGithub($path, $project)
	{
		cd "C:\WebDev\projects\$path"
		$msg = "Autosave"		
		git add .
    git add -u .
		git commit -m $msg
		git push $project master:master
	}  
  
	#####################
	## Выполнение push ##
	#####################	  
  PushProjectToGithub "csgosnipepot" "projects-csgosnipepot"

  
##################################################
## 2. Сохранение моих laravel-пакетов на github ##
##################################################
	
	# 2.1. Подготовка функции для залива свежих данных на github
	function PushToGithub($project, $packid)
	{
		cd "C:\WebDev\projects\$project\project\vendor\4gekkman\$packid"
		$msg = "Autosave to $branch"		
		git add .
    git add -u .
		git commit -m $msg
    git pull --commit $packid master
		git push $packid master:master	
	}	
  
  # 2.2. Подготовка списка пакетов, данные по которым надо залить 
  $laravelpacks = '[
    {
        "project": "csgohap",
        "packid": "M1"
    },
    {
        "project": "csgohap",
        "packid": "M2"
    },
    {
        "project": "csgohap",
        "packid": "M3"
    },
    {
        "project": "csgohap",
        "packid": "M4"
    },
    {
        "project": "csgohap",
        "packid": "M5"
    },
    {
        "project": "csgohap",
        "packid": "M8"
    },
    {
        "project": "csgohap",
        "packid": "D10000"
    },
    {
        "project": "csgohap",
        "packid": "D10003"
    },
    {
        "project": "csgohap",
        "packid": "D10004"
    },
    {
        "project": "csgohap",
        "packid": "L10000"
    },
    {
        "project": "csgohap",
        "packid": "L10001"
    },
    {
        "project": "csgohap",
        "packid": "R1"
    },
    {
        "project": "csgohap",
        "packid": "R2"
    },
    {
        "project": "csgohap",
        "packid": "R3"
    },
    {
        "project": "csgohap",
        "packid": "R4"
    },
    {
        "project": "csgohap",
        "packid": "R5"
    },
    {
        "project": "csgohap",
        "packid": "D10005"
    },
    {
        "project": "csgohap",
        "packid": "M10"
    },
    {
        "project": "csgohap",
        "packid": "M11"
    },
    {
        "project": "csgohap",
        "packid": "M9"
    },
    {
        "project": "csgohap",
        "packid": "D10006"
    },
    {
        "project": "csgohap",
        "packid": "L10003"
    },
    {
        "project": "csgohap",
        "packid": "D10009"
    },
    {
        "project": "csgohap",
        "packid": "M12"
    },
    {
        "project": "csgohap",
        "packid": "M13"
    },
    {
        "project": "csgohap",
        "packid": "D10010"
    },
    {
        "project": "csgohap",
        "packid": "D10011"
    },
    {
        "project": "csgohap",
        "packid": "L10004"
    },
    {
        "project": "csgohap",
        "packid": "R6"
    },
    {
        "project": "csgohap",
        "packid": "M14"
    },
    {
        "project": "csgohap",
        "packid": "M15"
    },
    {
        "project": "csgohap",
        "packid": "D10012"
    },
    {
        "project": "csgohap",
        "packid": "M16"
    },
    {
        "project": "csgohap",
        "packid": "M17"
    },
    {
        "project": "csgohap",
        "packid": "M18"
    },
    {
        "project": "csgohap",
        "packid": "D10013"
    },
    {
        "project": "csgohap",
        "packid": "M7"
    }
]' | ConvertFrom-Json	
	
  # 2.3. Осуществление залива
  #foreach ($pack in $laravelpacks) { 
  #  PushToGithub $pack.project $pack.packid
  #}
  
  
#################################################
## 3. Сохранение поддеревьев проекта на github ##
#################################################  
  
	########################
	## Подготовка функции ##
	########################	
	function PushSubreeToGithub($path, $prefix, $github)
	{
		cd "C:\WebDev\projects\$path"
		$msg = "Autosave"		
		git add .
    git add -u .
		git commit -m $msg
    git subtree push --squash --prefix=$prefix $github master  
	}  
  
	#####################
	## Выполнение push ##
	#####################	  
  PushSubreeToGithub "csgosnipepot" "stateless/projects-csgosnipepot-app" "git@github.com:4gekkman/projects-csgosnipepot-app.git"
  PushSubreeToGithub "csgosnipepot" "stateless/projects-csgosnipepot-mysql/" "git@github.com:4gekkman/projects-csgosnipepot-mysql.git"
  PushSubreeToGithub "csgosnipepot" "stateless/projects-csgosnipepot-redis/" "git@github.com:4gekkman/projects-csgosnipepot-redis.git" 
  PushSubreeToGithub "csgosnipepot" "stateless/projects-csgosnipepot-websockets/" "git@github.com:4gekkman/projects-csgosnipepot-websockets.git"
  
  
##########################################################
## n. Перейти в окне терминала в первоначальный каталог ##
##########################################################
## - Который был до запуска этого скрипта.
cd $current_path
Stop-SshAgent
	