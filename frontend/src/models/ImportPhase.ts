
type ImportPhase = {
  id: '1_UPLOAD',
} | {
  id: '2_UPLOADING',
  data: ArrayBuffer,
} | {
  id: '3_MISSING_EXAMINERS',
  data: ArrayBuffer,
  missingExaminers: string[],
  missingSubjects: string[]
} | {
  id: '4_MISSING_SUBJECTS',
  data: ArrayBuffer,
  missingExaminers: string[],
  missingSubjects: string[]
} | {
  id: '5_SUCCESS'
}

export default ImportPhase;
