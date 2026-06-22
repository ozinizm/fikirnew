import mysql, {
  RowDataPacket,
  ResultSetHeader,
  OkPacket,
  FieldPacket,
} from "mysql2/promise";

const pool = mysql.createPool({
  host: process.env.DB_HOST || "127.0.0.1",
  port: Number(process.env.DB_PORT) || 3306,
  database: process.env.DB_NAME || "fikircreative",
  user: process.env.DB_USER || "root",
  password: process.env.DB_PASSWORD || "",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  charset: "utf8mb4",
});

export type SqlParam = string | number | boolean | null | Buffer | Date;

// SELECT sorgular
export async function query<T extends RowDataPacket>(
  sql: string,
  params?: SqlParam[]
): Promise<T[]> {
  const [rows] = (await pool.execute(sql, params)) as [T[], FieldPacket[]];
  return rows;
}

// Tekil kayıt
export async function queryOne<T extends RowDataPacket>(
  sql: string,
  params?: SqlParam[]
): Promise<T | null> {
  const rows = await query<T>(sql, params);
  return rows[0] ?? null;
}

// INSERT / UPDATE / DELETE
export async function execute(
  sql: string,
  params?: SqlParam[]
): Promise<ResultSetHeader> {
  const [result] = (await pool.execute(sql, params)) as [
    ResultSetHeader | OkPacket,
    FieldPacket[]
  ];
  return result as ResultSetHeader;
}

export default pool;
