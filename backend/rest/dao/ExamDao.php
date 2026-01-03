<?php

class ExamDao
{

  private $connection;

  /**
   * constructor of dao class
   */
  public function __construct()
  {
    try {
      /** TODO
       * List parameters such as servername, username, password, schema. Make sure to use appropriate port
       */
      $DB_HOST = 'localhost';
      $DB_NAME = 'web-final';
      $DB_PORT = 3306;
      $DB_USER = 'root';
      $DB_PASSWORD = '';

      /** TODO
       * Create new connection
       */
      $this->connection = new PDO(
        "mysql:host=" . $DB_HOST . ";dbname=" . $DB_NAME . ";port=" . $DB_PORT,
        $DB_USER,
        $DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
      echo "Connected successfully";
    } catch (PDOException $e) {
      echo "Connemployees_performance_reportection failed: " . $e->getMessage();
    }
  }


  //helper function for querys from lab6
  protected function query($query, $params)
  {
    $stmt = $this->connection->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  protected function query_unique($query, $params)
  {
    $results = $this->query($query, $params);
    return reset($results);
  }

  /** TODO
   * Implement DAO method used to get employees performance report
   */
  public function employees_performance_report() {
    return $this->query("SELECT e.employeeNumber as id,
                                CONCAT(e.firstName, ' ', e.lastName) as full_name,
                                e.email,
                                SUM(p.amount) as total
                         FROM payments p
                         JOIN customers c ON c.customerNumber = p.customerNumber
                         JOIN employees e ON e.employeeNumber = c.salesRepEmployeeNumber
                         GROUP BY e.employeeNumber", []);
  }

  /** TODO
   * Implement DAO method used to delete employee by id
   */
  public function delete_employee($employee_id) {
    return $this->query("DELETE FROM employees WHERE employeenumber = :employeenumber", ['employeenumber' => $employee_id]);
  }

  /** TODO
   * Implement DAO method used to edit employee data
   */
  public function edit_employee($employee_id, $data) {
    return $this->query("UPDATE employees e
                         SET e.firstName = :firstName,
                             e.lastName = :lastName,
                             e.email = :email
                         WHERE e.employeeNumber = :employeeNumber;", [
                          'employeeNumber' => $employee_id,
                          'firstName' => $data['firstName'],
                          'lastName' => $data['lastName'],
                          'email' => $data['email']
                         ]);
  }

  //helper function for get employee data
  public function get_employee($employee_id) {
    return $this->query("SELECT e.firstName AS first_name,
                                e.lastName AS last_name,
                                e.email
                         FROM employees e
                         WHERE e.employeeNumber = :employeeNumber", ['employeeNumber' => $employee_id]);
  }

  /** TODO
   * Implement DAO method used to get orders report
   */
  public function get_orders_report() {
    return $this->query("SELECT GROUP_CONCAT(
                                  CONCAT('<tr>',
                                         '<td>', p.productName, '</td>',
                                         '<td>', od.quantityOrdered, '</td>',
                                         '<td>', od.priceEach, '</td></tr>')
                                  SEPARATOR ''
                                ) AS details,
                                o.orderNumber as order_number,
                                SUM(od.priceEach * od.quantityOrdered) as total_amount
                         FROM orders o
                         JOIN orderdetails od ON od.orderNumber = o.orderNumber
                         JOIN products p ON p.productCode = od.productCode
                         GROUP BY o.orderNumber", []);
  }

  /** TODO
   * Implement DAO method used to get all products in a single order
   */
  public function get_order_details($order_id) {
    return $this->query("SELECT p.productName,
                                od.quantityOrdered as quantity,
                                od.priceEach as price_each
                         FROM orderdetails od
                         JOIN products p ON p.productCode = od.productCode
                         WHERE od.orderNumber = :orderNumber;", ['orderNumber' => $order_id]);
  }
}
