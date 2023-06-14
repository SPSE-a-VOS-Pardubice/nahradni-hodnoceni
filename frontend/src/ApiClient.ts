import axios from 'axios';
import DashboardStatsData from './models/DashboardStatsData';
import FailedUploadResponse from './models/FailedUploadResponse';
import FilterParams from './models/FilterParams';
import Classroom from './models/data/Classroom';
import Exam from './models/data/Exam';
import Student from './models/data/Student';
import Subject from './models/data/Subject';
import Teacher from './models/data/Teacher';
import _Class from './models/data/_Class';

const BASE_URL = 'http://localhost:8080';

const DASHBOARD_PATH = '/api/1/dashboard';
const DATA_URL = '/api/1/data';
const UPLOAD_URL = '/api/1/upload';

export async function fetchDashboardStats(): Promise<DashboardStatsData> {
  return (await axios.get(BASE_URL + DASHBOARD_PATH + '/stats')).data;
}

export async function fetchExams(params: FilterParams): Promise<Exam[]> {
  const page = 0; // TODO
  return (await axios.get(BASE_URL + DASHBOARD_PATH + `/exams/${page}`, {params})).data;
}

export async function uploadData<T extends _Class | Classroom | Exam | Student | Subject | Teacher>(data: T, type: 'class' | 'classroom' | 'exam' | 'student' | 'subject' | 'teacher'): Promise<T> {
  return (await axios.post(BASE_URL + DATA_URL + `/${type}`, data)).data;
}

export async function uploadCsvFile(data: ArrayBuffer): Promise<'' | FailedUploadResponse> {
  const formData = new FormData();
  formData.append('file', new Blob([data]));

  return (await axios.post(BASE_URL + UPLOAD_URL, formData)).data;
}
