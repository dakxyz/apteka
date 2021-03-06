# Философия

## Глоссарий

- Бизнес-логика - описывает правила предметной области.
- Сервис - набор процедур объединенных общей бизне-логикой.
- Entity - объект описывающий некую сущность из предметной области.
- Сервис-владелец entity - это сервис, который описывает бизнес-логику для этой entity.
- DTO-объект - объект, который используется для передачи данных между подсистемами приложения. В нашем случае в качестве DTO используются объекты потомки **\Xyz\Akulov\Service\Core\Response\AbstractResponse**.
- Общее знание - структуры о которых могут знать все компоненты системы.
- Web-сервис - идентифицируемая веб-адресом программная система со стандартизированными интерфейсами.

## Принципы

- Любая бизне-логика - сервис.
- Любая entity - общее знание.
- У любой entity должен быть сервис-владелец, только он может работать с хранилищем этой entity напрямую.
- Любой публичный метод сервиса должен возвращать DTO-объект.
- Сервис не должен генерировать исключительные ситуации.
- Сервис не должен обрабатывать исключительные ситуации если этого не требует бизнес-логика.
- Исключительная ситуация - это не проблема, нет катасрофы в том, что пользователь увидит 500-ю страницу.
- В случае если сервис не может выполнить запрашиваемую операцию он может передать информацию об этом с помощью DTO-объекта.
- Сервис состоит из двух частей: абстракции и реализации. Абстракция включает в себя - entity, DTO-объекты и интерфейс сервиса. Реализация сервиса - это реализация интерфейса сервиса.
- Абстракция сервиса - общее знание.
- Любой сервис легко может быть заменен web-сервисом. В этом случае реализация сервиса - это rpc-клиент для веб-сервиса. 
- При возможности используйте в названиях классов и неймспейсов понятия в единственном числе.
