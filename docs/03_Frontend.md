# Фронтенд

Запускаем docker-контейнеры командой `docker-compose up -d`

## Добавляем шаблон для списка пользователей и рендерим его

1. Исправляем версию PHP в `composer.json`
    ```json
    "require": {
      "php": ">=8.1",
    ```
2. Заходим в контейнер `php` командой `docker exec -it php sh`. Дальнейшие команды выполняются из контейнера
3. Устанавливаем бандл для Twig командой `composer require symfony/twig-bundle`
4. Создаём класс `App\Entity\User`
```php
<?php

namespace App\Entity;

class User
{
    public function __construct(
        private string $firstName,
        private string $middleName,
        private string $lastName,
        private string $phone,
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function setMiddleName(string $middleName): void
    {
        $this->middleName = $middleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }
}
```
5. Создаём класс `App\Manager\UserManager`
```php
<?php
   
namespace App\Manager;
   
use App\Entity\User;
   
class UserManager
{
    /**
     * @return User[]
     */
    public function getUserList(): array
    {
        return [
            new User('Иван', 'Сергеевич', 'Сапогов', '+71112223344'),
            new User('Фёдор', 'Викторович', 'Лаптев', '+72223334455'),
            new User('Пётр', 'Михайлович', 'Стеклов', '+73334445566'),
            new User('Игнат', 'Глебович', 'Лопухов', '+74445556677'),
        ];
    }
}
```
6. Создаём файл `templates/list.twig`
```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>User list</title>
    </head>
    <body>
        <ul id="user.list">
            {% for user in users %}
                <li>{{ user.firstName }} {{ user.middleName }} {{ user.lastName }} ({{ user.phone }}) </li>
            {% endfor %}
        </ul>
    </body>
</html>
```
7. Исправляем класс `App\Controller\WorldController`:
    1. Наследуем класс от `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`
    2. Инжектим инстанс сервиса `App\Manager\UserManager` в конструкторе
```php
    public function __construct(
        readonly private UserManager $manager,
    ) {
    }
```
1. Исправляем метод `hello`
```php
public function hello(): Response
{
    return $this->render('list.twig', [
       'users' => $this->manager->getUserList(),
   ]);
}
```
8. Заходим по адресу `http://localhost:7777/world/hello`, видим список пользователей

## Добавляем пост-обработку значений полей через фильтры

1. Исправляем файл `templates/list.twig`, добавляя пост-обработку значений полей через фильтры
```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>User list</title>
    </head>
    <body>
        <ul id="user.list">
            {% for user in users %}
                <li>{{ user.firstName|upper }} {{ user.middleName|lower }} {{ user.lastName }} ({{ user.phone }}) </li>
            {% endfor %}
        </ul>
    </body>
</html>
```
2. Обновляем страницу в браузере, видим результат применения фильтров

## Добавляем наследование в шаблоны

1. Переименовываем файл `templates/base.html.twig` в `layout.twig`
2. Создаём файл `templates/user-content.twig`
```html
   {% extends 'layout.twig' %}
   
   {% block title %}
    User list
   {% endblock %}
   {% block body %}
   <ol id="user.list">
       {% for user in users %}
           <li>{{ user.firstName|upper }} {{ user.middleName|lower }} {{ user.lastName }} ({{ user.phone }}) </li>
       {% endfor %}
   </ol>
   {% endblock %}
```
3. Исправляем в классе `App\Controller\WorldController` метод `hello`
    ```php
    public function hello(): Response
    {
        return $this->render('user-content.twig', ['users' => $this->userManager->getUserList()]);
    }
    ```
4. Обновляем страницу в браузере, видим, что список стал нумерованным

## Вызов родительского блока

1. Исправляем файл `templates/user-content.twig`
```html
    {% extends 'layout.twig' %}
   
   {% block title %}
    User list {{ parent() }}
   {% endblock %}
   {% block body %}
   <ol id="user.list">
       {% for user in users %}
           <li>{{ user.firstName|upper }} {{ user.middleName|lower }} {{ user.lastName }} ({{ user.phone }}) </li>
       {% endfor %}
   </ol>
   {% endblock %}
```
2. Обновляем страницу, в title страницы должно быть "User list Welcome!"

## Добавляем макрос

1. Создаём файл `templates/macros.twig`
```html
{% macro renderUser(user) %}
    <li>({{ user.phone }}) {{ user.firstName }} {{ user.middleName }} {{ user.lastName }} </li>
{% endmacro %}
```
2. Исправляем файл `templates/user-content.twig`:
```html
{% extends 'layout.twig' %}

{% import 'macros.twig' as macros %}

{% block title %}
User table
{% endblock %}
{% block body %}
<ul id="user.list">
    {% for user in users %}
        {{ macros.renderUser(user) }}
    {% endfor %}
</ul>
{% endblock %}
```
3. Обновляем страницу в браузере, видим, что в таблице первой колонкой стала колонка "Телефон"

## Добавляем собственные расширения

1. Создаем папку `src/Extension`
2. В ней создаем файл MyExtension.php
3. Наследуем его от `Twig\Extension\AbstractExtension`
4. Проверяем появились ли фильтры и функции в списке с помощью `bin/console debug:twig` (внутри контейнера)
```php
class MyExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('my_filter', [$this, 'myFilter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('my_function', [$this, 'myFunction']),
        ];
    }

    public function myFilter()
    {
        return 'myFilter myFilter';
    }

    public function myFunction()
    {
        return 'myFunction myFunction';
    }
}
```

## Устанавливаем Webpack Encore и подключаем таблицы стилей JavaScript в шаблон

1. Устанавливаем Webpack Encore командой `composer require symfony/webpack-encore-bundle`
2. Устанавливаем `yarn` в контейнере (либо добавляем в образ, если планируем использовать дальше) командой
   `apk add yarn`
3. Устанавливаем зависимости командой `yarn install`
4. Устанавливаем загрузчик для работы с SASS командой `yarn add sass-loader@^10.0.0 node-sass@^6.0.0 --dev`
5. Устанавливаем bootstrap командой `yarn add bootstrap --dev`
6. Устанавливаем плагины для работы с Vue.js `yarn add vue@^2.6.0 vue-loader@^15.9.5 vue-template-compiler --dev`
7. Выполняем сборку для dev-окружения командой `yarn dev`
8. Видим собранные файлы в директории `public/build`
9. Выполняем сборку для prod-окружения командой `yarn build`
10. Видим собранные файлы в директории `public/build`, которые обфусцированы и содержат хэш в имени
11. В файле `templates/layout.twig` убираем комментарии с вызовов макросов для загрузки CSS и JS если они есть
     ```html
     <!DOCTYPE html>
     <html>
         <head>
             <meta charset="UTF-8">
             <title>{% block title %}Welcome!{% endblock %}</title>
             {% block stylesheets %}
                 {{ encore_entry_link_tags('app') }}
             {% endblock %}
    
             {% block javascripts %}
                 {{ encore_entry_script_tags('app') }}
             {% endblock %}
         </head>
         <body>
             {% block body %}{% endblock %}
         </body>
     </html>   
     ```
12. Выполняем сборку для dev-окружения командой `yarn dev`
13. Обновляем страницу в браузере, видим, что фон стал серым, т.е. CSS-стили загрузились

## Используем SASS вместо CSS

1. Переименовываем файл `assets/styles/app.css` в `app.scss` и исправляем его
```scss
$color: orange;

body {
    background-color: $color;
}
```
2. Исправляем файл `assets/app.js`
    ```js
    /*
     * Welcome to your app's main JavaScript file!
     *
     * We recommend including the built version of this JavaScript file
     * (and its CSS file) in your base layout (base.html.twig).
     */
    
    // any CSS you import will output into a single css file (app.css in this case)
    import './styles/app.scss';
    
    // start the Stimulus application
    import './bootstrap';
    ```
3. Исправляем файл `webpack.config.js`, убираем комментарий в строке 59 (`//.enableSassLoader()`)
4. Выполняем сборку для dev-окружения командой `yarn encore dev`
5. Обновляем страницу в браузере, видим, что фон стал оранжевым, т.е. SASS-компилятор отработал

## Подключаем bootstrap

1. Исправляем файл `assets/styles/app.scss`
    ```scss
    @import "~bootstrap/scss/bootstrap";
    
    $color: orange;
    
    body {
        background-color: $color;
    }
    ```
2. Исправляем файл `user-content.twig`
```html
{% extends 'layout.twig' %}

{% import 'macros.twig' as macros %}

{% block title %}
User table
{% endblock %}
{% block body %}
<ul id="user.list" class="list-group">
    {% for user in users %}
        {{ macros.renderUser(user) }}
    {% endfor %}
</ul>
{% endblock %}
```
3. Исправляем файл `templates/macros.twig`
```html
{% macro renderUser(user) %}
    <li class="list-group-item">{{ user.firstName }} {{ user.middleName|upper }} {{ user.lastName }} ({{ user.phone }}) </li>
{% endmacro %}
```
4. Выполняем сборку для dev-окружения командой `yarn dev`
5. Обновляем страницу в браузере, видим, что bootstrap-стили применились

## Добавляем простое приложение на Vue.js

1. Добавляем в файл `webpack.config.js` 60-ю строку `.enableVueLoader()`
2. Создаём файл `assets/components/App.vue`
    ```vue
    <template>
        <table class="table table-hover">
            <thead>
                <tr><th>Имя</th><th>Отчество</th><th>Фамилия</th><th>Телефон</th></tr>
            </thead>
            <tbody>
                <tr v-for="user in users">
                    <td v-for="key in columns">
                        {{ user[key] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </template>
    
    <script>
        export default {
            data() {
                return {
                    users: [],
                    columns: ['firstName', 'middleName', 'lastName', 'phone']
                };
            },
            mounted() {
                let data = document.querySelector("div[data-users]");
                let userList = JSON.parse(data.dataset.users);
    
                this.users.push.apply(this.users, userList);
            }
        };
    </script>
    ```
3. Создаём файл `templates/user-vue.twig`
    ```html
    {% extends 'layout.twig' %}

    {% block title %}
    User list
    {% endblock %}
    {% block body %}
    <div ref="users" data-users="{{ users }}"></div>

    <div id="app"></div>
    {% endblock %}
    ```
4. Исправляем класс `App\Entity\User`, добавляя новый метод `toArray`
    ```php
    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'lastName' => $this->lastName,
            'phone' => $this->phone,
        ];
    }
    ```
5. Исправляем класс `App\Manager\UserManager`, добавляя новый метод `getUserAsArrayList`
    ```php
    public function getUserAsArrayList(): array
    {
        return array_map(
            static fn(User $user) => $user->toArray(),
            $this->getUserList(),
        );
    }
    ```
6. Исправляем в классе `App\Controller\WorldController` метод `hello`
    ```php
    /**
     * @throws JsonException
     */
    public function hello(): Response
    {
        return $this->render('user-vue.twig', ['users' => json_encode($this->manager->getUserAsArrayList(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)]);
    }
    ```
7. Исправляем файл `assets/app.js`
    ```js
    import './styles/app.scss';
    import Vue from 'vue';
    import App from './components/App';
    import './bootstrap';
    
    new Vue({
        el: '#app',
        render: h => h(App)
    });
    ```
8. Выполняем сборку для dev-окружения командой `yarn dev`
9. Обновляем страницу в браузере, видим, что таблица отображается через Vue-приложение
