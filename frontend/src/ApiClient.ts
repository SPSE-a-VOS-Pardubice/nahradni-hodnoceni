import axios from "axios";
import DashboardStats from "./models/DashboardStats";

const BASE_URL = "http://localhost:8080"
const DASHBOARD_PATH = "/api/1/dashboard"

export async function getDashboardStats(): Promise<DashboardStats> {
  return (await axios.get(BASE_URL + DASHBOARD_PATH + "/stats")).data;
}
