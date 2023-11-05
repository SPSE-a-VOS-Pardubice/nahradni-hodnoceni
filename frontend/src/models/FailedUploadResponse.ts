
export type missingExaminerDetails = {name: string, surname: string};

interface FailedUploadResponse {
  missingSubjects: string[]
  missingExaminers: missingExaminerDetails[]
}

export default FailedUploadResponse;
