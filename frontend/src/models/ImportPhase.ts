
type ImportPhase = {
    phase: '0_CLOSED',
} | {
    phase: '1_UPLOAD',
} | {
    phase: '2_UPLOADING',
    data: ArrayBuffer,
} | {
    phase: '3_MISSING_EXAMINERS',
    data: ArrayBuffer,
    missingExaminers: string[],
    missingSubjects: string[]
} | {
    phase: '4_MISSING_SUBJECTS',
    data: ArrayBuffer,
    missingExaminers: string[],
    missingSubjects: string[]
} | {
    phase: '5_SUCCESS'
}

export default ImportPhase;
