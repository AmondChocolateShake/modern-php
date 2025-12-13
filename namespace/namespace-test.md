# 배경

---

PHP의 네임스페이스는 5.3.0버전에서 도입

운영체제 파일 디렉토리 시스템과 유사하게 가상 계층 구조를 구성할 수 있는 도구

여러 프레임워크를 쓰는 경우 서로 이름이 같은 클래스가 사용되어도 충돌하지 않음 → 네임스페이스로 구속(sandboxed)하기 때문

# 1. 네임스페이스

---

### [예제 1]

https://github.com/symfony/http-foundation

```php
<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

// Help opcache.preload discover always-needed symbols
class_exists(ResponseHeaderBag::class);

/**
 * Response represents an HTTP response.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Response
{
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;            // RFC2518
    public const HTTP_EARLY_HINTS = 103;           // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
 ...
```

⇒ Response란 객체는 `namespace Symfony\Component\HttpFoundation;` 의 하위 계층으로 정의됨

<aside>

💡 use 키워드를 이용해 임포트한 코드는 PHP파일 최상단의 <?php 태그 아래에 가장 먼저 정의 되어야한다.

</aside>

### [예제 2]

```php
<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\MyCustomResponse;

// Help opcache.preload discover always-needed symbols
class_exists(ResponseHeaderBag::class);

/**
 * Response represents an HTTP response.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Response
{
    
 ...
```

⇒ 만약 같은 이름의 클래스를 하나 더 정의해서 사용하고 싶은 경우 위와 같이 네임스페이스를 변경하여 정의하면 클래스 이름 충돌 없이 가능

### [예제 3]

```php
<?php
use Symfony\Component\HttpFoundation as Response;
use Symfony\Component\MyCustomResponse as MyResponse;

$response = new Response;
$myResponse = new MyResponse;
...

```

<aside>

💡 네임스페이스는 가상의 개념이며 디렉토리와 1:1로 대치할 필요가 없음(그러나 **PSR-4 오토로더** 표준과의 호환성을 위해 서브네임스페이스를 파일 시스템 디렉터리에 맞춰 개발하는게 일반적)

</aside>

## 1.1 전역 네임스페이스

---

**전역 네임스페이스**는 파일 최상위에 위치한 PHP 파일에서 네임스페이스 선언 없이 정의하는 함수를 호출할 수 있는 문법이다.

- PHP의 모든 **기본 내장함수**는 전역 네임스페이스를 따른다.
- 상수, 함수, 클래스 정의 시 namespace를 선언하지 않는 경우 **전역 네임스페이스**에 해당한다.

### [예제 1]

```php
<?php

// 1. MyLogger 클래스는 'namespace' 선언이 없으므로 전역 네임스페이스에 속합니다.
class MyLogger
{
    public static function log($message)
    {
        echo "🌍 [Global Log] " . $message . "\n";
    }
}

// 2. 전역 네임스페이스에 있는 클래스는 네임스페이스 없이 바로 접근 가능합니다.
MyLogger::log("이 메시지는 전역 클래스에서 기록됩니다.");

// 3. PHP 내장 클래스 (예: DateTime)도 전역 네임스페이스에 있습니다.
$date = new DateTime(); 
echo "현재 시간: " . $date->format('Y-m-d H:i:s') . "\n";

?>
```

⇒ namespace 선언이 없으므로 MyLogger 클래스는 전역 네임스페이스 계층에 해당한다. 네임스페이스 선언 없이 바로 사용이 가능하다. PHP 기본 내장 클래스인 DateTime도 같은 원리로 사용됨.

### [예제 2]

```php
<?php

/*** 전역 네임스페이스 ***/
class MyClass
{
    public function identify()
    {
        echo "나는 🌍 전역(Global) 네임스페이스의 MyClass입니다.\n";
    }
}

/*** 네임스페이스 ***/
namespace App\Model; 

class MyClass
{
    public function identify()
    {
        echo "나는 📦 App\\Model 네임스페이스의 MyClass입니다.\n";
    }
}

$namespacedClass = new MyClass(); // PHP는 현재 네임스페이스(App\Model)에서 찾습니다.
$namespacedClass->identify();     // 출력: 📦 App\Model 네임스페이스의 MyClass입니다.

$globalClass = new \MyClass();  // <--- \를 붙여 전역 네임스페이스임을 강제
$globalClass->identify();      // 출력: 🌍 전역(Global) 네임스페이스의 MyClass입니다.

?>
```

⇒ 전역 네임스페이스에서 정의된 클래스를 사용하기 위해서는 앞에 백슬래시(\)를 붙여서 FQN(절대 경로)임을 명시해야 합니다.

## 1.2 왜 전역 네임스페이스의 호출 방식이 다른가?

---

위 두 개의 예제를 보면 전역 네임스페이스의 클래스를 호출하는 방식이 다르다. 예제 1은 \없이 클래스 호출, 예제2는 \를 사용해 클래스 호출.

 

**→ 이유는** **클래스가 선언된 계층과 클래스를 호출하는 계층이 전역인지에 따라 다르다.**

### [예제 1]

```php
<?php
// 전역 네임스페이스 (Global Scope)

class MyLogger { /* ... */ }

// 호출 시점: 전역 네임스페이스
MyLogger::log("전역 클래스에서 기록됩니다."); 

?>
```

⇒ MyLogger 클래스는 **네임스페이스 선언이 없는 곳에서 정의**했으므로 **전역 네임스페이스**이다.

→ 이런 경우 MyLogger 클래스를 아무런 접두사 없이 바로 접근 가능

### [예제 2]

```php
<?php

namespace App\Model;

class MyLogger{ /* ... */ }

//App\Model 네임스페이스 클래스 사용
$myLogger = use MyLogger;

//전역 네임스페이스 클래스 사용
$globalMyLogger = use \MyLogger;

?>
```

⇒ `$myLogger` 변수에 담긴 **`MyLogger`** 객체는 `namespace App\Model`에 해당하는 **`MyLogger`** 클래스이다.

→ `$globalMyLogger` 변수에 담긴 **`MyLogger`**객체를 선언하는 위치는 **`App\Model`** 네임스페이스 영역이므로 전역 네임스페이스를 사용하려면 `\` 를 사용해 호출해줘야한다. 

**`namespace App\Model;`** 이라는 선언을 하는 순간부터 PHP는 모든 코드를 가상의 계층을 만들어 묶는다. 해당 계층 밖의 클래스(변수,함수)를 사용하려면 `\`와 같은 특별한 참조문법이 필요한 것.

## 1.3 함수, 상수 네임스페이스

---

PHP 5.6버전 부터 함수와 상수를 임포트할 수 있게 되었다.

이를 사용하기 위해 약간의 문법이 추가된다.

### [함수 임포트 예제]

```php
<?php

use func Namespace\functionName;

functionName();
```

⇒ use 뒤에 func 키워드를 추가하면 함수를 임포트 할 수 있다.

### [상수 임포트 예제]

```php
<?php

use constant Namespace\CONST_NAME;

echo CONST_NAME;
```

⇒ use 뒤에 constant 키워드를 추가하면 상수를 임포트 할 수 있다.

# 2. 유용한 팁

---

개발 시 유용한 몇 가지 팁과 권고사항 

## 2.1 한 번에 임포트 하기

---

다수의 클래스, 인터페이스, 함수, 상수를 하나의 PHP 파일에 임포트하려면 다수의 use 키워드를 써야한다.

이런 다수의 use 구문을 축약할 수 있다.

### [예제 1]

```php
<?php

use Symfony\Component\HttpFoundation\Request,
		Symfony\Component\HttpFoundation\Response,
		Symfony\Component\HttpFoundation\Cookie;
```

⇒ use 와 , 를 사용해 use가 반복 사용되는 것을 축약할 수 있다.

→ **하지만 코드가 엉키거나 유지보수성이 떨어지기 때문에 사용하면 안된다.**

### [좋은 예]

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
```

⇒ **무조건 한 번의 임포트 당 하나의 use를 사용해 선언한다.**

→ 가독성이 올라가고 쉽게 코드를 변경할 수 있게 된다.

## 2.2 복수의 네임스페이스를 한 파일에 정의하기

---

하나의 PHP 파일에 다수의 네임스페이스를 정의할 수 있다.

```php
<?php

namespace Foo(

class Foo{ /* ... */}

function getName(){ /* ... */ }

$NAME = 'David Foo';

)

namespace Bob(

class Bob{ /* ... */}

function getName(){ /* ... */ }

$NAME = 'David Bob';

)

```

⇒ 위와 같이 여러 네임스페이스를 한 번에 정의하는 것이 가능하다.

→ 이런 방식은 가능은 하지만 좋은 관행에 어긋난다. 

→ **하나의 파일에는 하나의 네임스페이스만 사용한다.** 

## 2.3 전역 네임스페이스 참조

---

- 네임스페이스 없이 클래스, 함수, 상수 등을 참조하면 PHP에서는 해당 코드가 실행되는 네임스페이스 계층에서 참조 대상이 존재한다고 간주한다.
- 현재 네임스페이스에서 ‘클래스’를 찾지 못하면 전역 네임스페이스에서 해당 참조 대상을 찾아본다.

<aside>

💡클래스 찾는 흐름

[ 현재 네임스페이스 → 없음 → 전역 네임스페이스 → 찾음 ]

⇒ **함수와 상수는 더 엄격한 규칙**을 따르며, 전역 네임스페이스로 자동으로 넘어가지 않습니다. 오직 **함수/상수의 이름이 PHP 내장 함수/상수**인 경우에만 예외적으로 전역 이름을 사용합니다.

</aside>