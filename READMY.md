#  Sys4

Четвертая попытка

## Разворот проекта

```bash
$ composer create-project symfony/skeleton:"^5.4" sys4
$ cd sys4
$ git init
```
дальше всякое с гит: клонирование, подключение первый коммит.

Пакеты:
```bash
$ composer install
$ composer require mpakfm/printu
```
Пишем путь в /src/Kernel.php:
![](/var/www/sys4/readmy/kernel.png)

```bash
$ composer require annotations
$ composer require twig
$ composer require form validator
$ composer require symfonycasts/verify-email-bundle
$ composer require symfony/mailer
$ composer require mailgun-mailer
$ composer require symfony/orm-pack
$ composer require --dev symfony/maker-bundle
$ composer require --dev symfony/profiler-pack
```

### Конфигурация БД

создаем в корне .env.local:
```bash
DATABASE_URL=mysql://root:123@127.0.0.1:3306/sys4

```
Создание БД средствами Symfony:
```bash
php bin/console doctrine:database:create
```
Пользователи и доступы:
```bash
$ composer require symfony/security-bundle
$ php bin/console make:user
```
На все интерактивные вопросы нажать Enter (принять то что установлено по умолчанию)

Добавим пару полей в модель /src/Entity/User:
```php
    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $lastName;
```

```php
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
```
Миграция и применение
```bash
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
```

Открыть в броузере: http://sys4.site/register

Если будет показана ошибка 502 
И в логе nginx будет такая строка:
`*67 upstream sent too big header while reading response header from upstream`
в настройках хоста в nginx прописать после инструкции `fastcgi_pass`:
```nginx configuration
    ## TUNE buffers to avoid error ##  
    fastcgi_buffers 16 32k;
    fastcgi_buffer_size 64k;
    fastcgi_busy_buffers_size 64k;
```
