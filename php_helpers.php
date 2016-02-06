<?php
////======================================================////
////																										  ////
////              	Библиотека php-хелперов	              ////
////																											////
////======================================================//*/
//// 			        		         ////
//// 	   Подключение классов	 ////
//// 			         		         ////
////===========================////

// Классы, поставляемые Laravel
use Illuminate\Routing\Controller as BaseController,
    Illuminate\Support\Facades\App,
    Illuminate\Support\Facades\Artisan,
    Illuminate\Support\Facades\Auth,
    Illuminate\Support\Facades\Blade,
    Illuminate\Support\Facades\Bus,
    Illuminate\Support\Facades\Cache,
    Illuminate\Support\Facades\Config,
    Illuminate\Support\Facades\Cookie,
    Illuminate\Support\Facades\Crypt,
    Illuminate\Support\Facades\DB,
    Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\Event,
    Illuminate\Support\Facades\File,
    Illuminate\Support\Facades\Hash,
    Illuminate\Support\Facades\Input,
    Illuminate\Foundation\Inspiring,
    Illuminate\Support\Facades\Lang,
    Illuminate\Support\Facades\Log,
    Illuminate\Support\Facades\Mail,
    Illuminate\Support\Facades\Password,
    Illuminate\Support\Facades\Queue,
    Illuminate\Support\Facades\Redirect,
    Illuminate\Support\Facades\Redis,
    Illuminate\Support\Facades\Request,
    Illuminate\Support\Facades\Response,
    Illuminate\Support\Facades\Route,
    Illuminate\Support\Facades\Schema,
    Illuminate\Support\Facades\Session,
    Illuminate\Support\Facades\Storage,
    Illuminate\Support\Facades\URL,
    Illuminate\Support\Facades\Validator,
    Illuminate\Support\Facades\View;

////======================================================//*/
//// 			         ////
//// 	   Функции	 ////
//// 			         ////
////===============////

  //-----//
  // r4_ //
  //-----//
  if(!function_exists('r4_')) {
    /**
     *  <h1>Список хелперов пакета R1</h1>
     *  <pre>
     *    r4_validate              | Проводит валидацию выбранного значения
     *  </pre>
     * @return bool
     */
    function r4_() {

      return true;

    }
  } else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r4_, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r4_, поскольку такая уже есть!', ['R1','r4_']);
  }

  //-------------//
  // r4_validate //
  //-------------//
  if(!function_exists('r4_validate')) {
    /**
     *
     *  <h1>Пример значения $values</h1>
     *  <pre>
     *    ["id" => 1, "name" => "ivan"]
     *  </pre>
     *  <h1>Пример значения $rules</h1>
     *  <pre>
     *    ["id" => "required|digits|max:255", "name" => "sometimes"]
     *  </pre>
     *  <h1>Стандартные правила валидации</h1>
     *  <pre>
     *    accepted             | The field under validation must be yes, on, 1, or true. This is useful for validating "Terms of Service" acceptance.
     *    active_url           | The field under validation must be a valid URL according to the checkdnsrr PHP function.
     *    after:date           | The field under validation must be a value after a given date. The dates will be passed into the strtotime PHP function.
     *    alpha                | The field under validation must be entirely alphabetic characters.
     *    alpha_dash           | The field under validation may have alpha-numeric characters, as well as dashes and underscores.
     *    alpha_num            | The field under validation must be entirely alpha-numeric characters.
     *    array                | The field under validation must be a PHP array.
     *    before:date          | The field under validation must be a value preceding the given date. The dates will be passed into the PHP strtotime function.
     *    between:min,max      | The field under validation must have a size between the given min and max. Strings, numerics, and files are evaluated in the same fashion as the size rule.
     *    boolean              | The field under validation must be able to be cast as a boolean. Accepted input are true, false, 1, 0, "1", and "0".
     *    confirmed            | The field under validation must have a matching field of foo_confirmation. For example, if the field under validation is password, a matching password_confirmation field must be present in the input.
     *    date                 | The field under validation must be a valid date according to the strtotime PHP function.
     *    date_format:format   | The field under validation must match the given format. The format will be evaluated using the PHP date_parse_from_format function. You should use either date or date_format when validating a field, not both.
     *    different:field      | The field under validation must have a different value than field.
     *    digits:value         | The field under validation must be numeric and must have an exact length of value.
     *    digits_between::min,max  | The field under validation must have a length between the given min and max.
     *    email                | The field under validation must be formatted as an e-mail address.
     *    exists:table,column  | The field under validation must exist on a given database table. 'state' => 'exists:states,abbreviation'
     *    image                | The file under validation must be an image (jpeg, png, bmp, gif, or svg)
     *    in:foo,bar,...       | The field under validation must be included in the given list of values.
     *    integer              | The field under validation must be an integer.
     *    ip                   | The field under validation must be an IP address.
     *    jSON                 | The field under validation must a valid JSON string.
     *    max:value            | The field under validation must be less than or equal to a maximum value. Strings, numerics, and files are evaluated in the same fashion as the size rule.
     *    mimes:foo,bar,...    | The file under validation must have a MIME type corresponding to one of the listed extensions. 'photo' => 'mimes:jpeg,bmp,png'
     *    min:value            | The field under validation must have a minimum value. Strings, numerics, and files are evaluated in the same fashion as the size rule.
     *    not_in:foo,bar,...   | The field under validation must not be included in the given list of values.
     *    numeric              | The field under validation must be numeric.
     *    regex:pattern        | The field under validation must match the given regular expression. When using the regex pattern, it may be necessary to specify rules in an array instead of using pipe delimiters, especially if the regular expression contains a pipe character.
     *    required             | The field under validation must be present in the input data and not empty. A field is considered "empty" if one of the following conditions are true: null, empty string, empty array, uploaded file with no path
     *    required_if:anotherfield,value,...      | The field under validation must be present if the anotherfield field is equal to any value.
     *    required_unless:anotherfield,value,...  | The field under validation must be present unless the anotherfield field is equal to any value.
     *    required_with:foo,bar,...               | The field under validation must be present only if any of the other specified fields are present.
     *    required_with_all:foo,bar,...           | The field under validation must be present only if all of the other specified fields are present.
     *    required_without:foo,bar,...            | The field under validation must be present only when any of the other specified fields are not present.
     *    required_without_all:foo,bar,...        | The field under validation must be present only when all of the other specified fields are not present.
     *    same:field           | The given field must match the field under validation.
     *    size:value           | The field under validation must have a size matching the given value. For string data, value corresponds to the number of characters. For numeric data, value corresponds to a given integer value. For files, size corresponds to the file size in kilobytes.
     *    string               | The field under validation must be a string.
     *    timezone             | The field under validation must be a valid timezone identifier according to the timezone_identifiers_list PHP function.
     *    unique:table,column,except,idColumn     | The field under validation must be unique on a given database table. If the column option is not specified, the field name will be used.
     *    url                  | The field under validation must be a valid URL according to PHP's filter_var function.
     *  </pre>
     *  <h1>Избранные стандартные правила валидации</h1>
     *  <pre>
     *    sometimes            | Validate only if that field presents in $values
     *    required             | That field must present in $values
     *    regex:pattern        | Must match regex. Example: regex:/^[DLW]{1}[0-9]+$/ui
     *    numeric              | Must be a number
     *    digits:value         | Must be numeric and must have an exact length of value
     *    in:foo,bar,...       | Must be in list of foo,bar,...
     *    boolean              | Must be: true, false, 1, 0, "1", and "0".
     *    email                | Must be formatted as an e-mail address
     *    max:value            | Must be less than or equal to a maximum value
     *    min:value            | Must be more than or equal to a minimum value
     *    image                | Must be an image (jpeg, png, bmp, gif, or svg)
     *    mimes:foo,bar,...    | Must have a MIME type corresponding to one of the listed
     *  </pre>
     *  <h1>Кастомные правила валидации</h1>
     *  <pre>
     *    r4_numpos            | Must be a positive integer
     *    r4_numnn             | Must be not negative positive integer
     *  </pre>
     *
     * @param  string $values
     * @param  array $rules
     *
     * @return mixed
     */
    function r4_validate($values, $rules) {
      try {

        // 1. Создать объект-валидатор
        $validator = Validator::make($values, $rules);

        // 2. Если валидация провалилась
        if($validator->fails()) {

          // Вернуть сериализованный объект с ошибками валидации
          return [
              "status" => -1,
              "data"   => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)
          ];

        }

        // 3. Если валидация не провалилась
        return [
            "status" => 0,
            "data"   => ""
        ];

      } catch(\Exception $e) {

        write2log('Произошёл сбой в хелпере r4_validate: '.$e->getMessage());
        return [
          "status" => -1,
          "data"   => $e->getMessage()
        ];

      }
    }
  } else {
      \Log::info('Внимание! Пакету R4 не удалось определить функцию r4_validate, поскольку такая уже есть!');
      write2log('Внимание! Пакету R4 не удалось определить функцию r4_validate, поскольку такая уже есть!', ['R4','r4_validate']);
    }
