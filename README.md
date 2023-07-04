# Логіка відправки даних на кур'єрську службу (наприклад, Нова пошта)

DeliveryController має функцію createPackage, яка приймає дані про отримувача та посилку. 
Відбувається валідація даних та створення відповідних об'єктів для збереження в БД.
Після цього викликається функція sendDelivery, яка надсилає запит до Нової пошти з необхідною інформацією.

Якщо буде декілька кур'єрських служб, то api кожної служби можна зберегти в окремому файлі, а при надсиланні даних в createPackage, додати інформацію про кур'єрську службу.
Далі в залежності від служби обрати необхідне api та передати його в sendDelivery. 
