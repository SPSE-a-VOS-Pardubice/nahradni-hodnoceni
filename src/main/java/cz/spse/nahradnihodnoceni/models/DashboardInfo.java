package cz.spse.nahradnihodnoceni.models;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@AllArgsConstructor
@NoArgsConstructor
@Data
public class DashboardInfo {
    private int totalNH;
    private int finishedNH;

    private int totalOZ;
    private int finishedOZ;

    // absolutní hodnoty, ne procenta
    private int succeeded;
    private int failed;
    private int unmarked;
}
