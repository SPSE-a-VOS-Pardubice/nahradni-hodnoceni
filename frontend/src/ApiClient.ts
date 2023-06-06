import axios from "axios";
import DashboardStats from "./models/DashboardStats";
import Exam from "./models/Exam";
import FilterParams from "./models/FilterParams";

const BASE_URL = "http://localhost:8080"

const DASHBOARD_PATH = "/api/1/dashboard"
const DATA_URL = "/api/1/data"

export async function fetchDashboardStats(): Promise<DashboardStats> {
  return (await axios.get(BASE_URL + DASHBOARD_PATH + "/stats")).data;
}

export async function fetchExams(params: FilterParams, page: number): Promise<Exam[]> {
  return (await axios.get(BASE_URL + DASHBOARD_PATH + `/exams/${page}`, { params })).data;
}

export async function uploadExam(exam: Exam): Promise<Exam> {
  return (await axios.post(BASE_URL + DATA_URL + `/exam`, exam)).data;
}
