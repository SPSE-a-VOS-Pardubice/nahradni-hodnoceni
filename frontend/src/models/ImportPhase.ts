import {Period} from '../contexts/PeriodContext';
import {missingExaminerDetails} from './FailedUploadResponse';

type ImportPhase = {
  id: '1_UPLOAD',
} | {
  id: '2_UPLOADING',
  data: ArrayBuffer,
  period: Period
} | {
  id: '3_MISSING_EXAMINERS',
  data: ArrayBuffer,
  period: Period,
  missingExaminers: missingExaminerDetails[],
  missingSubjects: string[]
} | {
  id: '4_MISSING_SUBJECTS',
  data: ArrayBuffer,
  period: Period,
  missingExaminers: missingExaminerDetails[],
  missingSubjects: string[]
} | {
  id: '5_SUCCESS'
}

export default ImportPhase;
