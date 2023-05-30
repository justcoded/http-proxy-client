Webhooks Proxy Client. Powered by [Laravel Zero](https://github.com/laravel-zero/laravel-zero)
===================================

BASE STRUCTURE
-------------------
```
  build/		contains docker container config
  runtime/
    |-- bash/		Bash history (composer cache, bash commands history)
  src/			Laravel Zero application code (Project)
```

INSTALLATION
------------

Project can be setup with Docker.

(If you have a Mac machine - we recommend to run docker under Vagrant. See [Vagranfile and Instructions](https://bitbucket.org/snippets/justcoded/Aex4nL/))

1. Clone repository
2. Navigate to your project directory
3. Run `make init`, this command will copy important files from examples:
    - .env
    - docker-compose.yml
4. Check the .env file for correct configurations.
5. Login to JC docker hub: `docker login hub.jcdev.net:24000`
6. Run installation `make install`  

### Docker PHP Container

To get inside PHP container to run composer/php commands run this command:

`make php-bash`

Inside PHP container there is also GNU Make utility, run `make` without any parameters to get available commands list.

You're ready to write your code!
------------
