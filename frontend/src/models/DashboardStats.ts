
export default interface DashboardStats {
    totalNH: number;
    finishedNH: number;

    totalOZ: number;
    finishedOZ: number;

    succeeded: number;
    failed: number;
    unmarked: number;
}