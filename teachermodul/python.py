from flask import Flask, jsonify
import psycopg2

app = Flask(__name__)

# Database connection
conn = psycopg2.connect(
    host="localhost",
    dbname="college",
    port="5432",
    user="postgres",
    password="1234"
)

@app.route('/students', methods=['GET'])
def get_students():
    cur = conn.cursor()
    cur.execute("SELECT * FROM student;")
    students = cur.fetchall()
    cur.close()
    return jsonify(students)

if __name__ == '__main__':
    app.run(debug=True)
