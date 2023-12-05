import axios from 'axios';
import FailedUploadResponse from '../models/FailedUploadResponse';
import Classroom from '../models/data/Classroom';
import Exam from '../models/data/Exam';
import Student from '../models/data/Student';
import Subject from '../models/data/Subject';
import Teacher from '../models/data/Teacher';
import _Class from '../models/data/_Class';
import {Period} from '../contexts/PeriodContext';
import {PeriodRange} from '../models/PeriodRange';

const BASE_URL = 'http://localhost:8080';

const DASHBOARD_PATH = '/api/1/dashboard';
const DATA_URL = '/api/1/data';
const UPLOAD_URL = '/api/1/upload';

export async function fetchExams(period: Period): Promise<Exam[]> {
  const url = BASE_URL + DASHBOARD_PATH + `/exams/${period.year}/${period.period}`;
  return (await axios.get(url)).data;
}

export async function fetchClassrooms(): Promise<Classroom[]> {
  const url = BASE_URL + DATA_URL + `/classroom/list`;
  return (await axios.get(url)).data;
}

export async function fetchPeriodRange(): Promise<PeriodRange | null> {
  const url = BASE_URL + DASHBOARD_PATH + `/exams/period-range`;
  return (await axios.get(url)).data;
}

export async function uploadData<T extends _Class | Classroom | Exam | Student | Subject | Teacher>(data: T, type: 'class' | 'classroom' | 'exam' | 'student' | 'subject' | 'teacher'): Promise<T> {
  return (await axios.post(BASE_URL + DATA_URL + `/${type}`, data)).data;
}

export async function uploadCsvFile(data: ArrayBuffer, period: Period): Promise<'' | FailedUploadResponse> {
  const formData = new FormData();
  formData.append('file', new Blob([data]));

  return (await axios.post(BASE_URL + UPLOAD_URL, formData, {
    params: period,
  })).data;
}
