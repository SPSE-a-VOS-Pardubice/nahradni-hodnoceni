
export type sortByOptions = "student" | "teacher" | "class" | "mark"

export default class FilterParams {
    status?: "finished" | "unfinished"
    type?: "nahradni_hodnoceni" | "opravna_zkouska"
    success?: "successful" | "failed"
    mark?: "1" | "2" | "3" | "4" | "5"
    sortBy?: sortByOptions
    reverse?: boolean
}
