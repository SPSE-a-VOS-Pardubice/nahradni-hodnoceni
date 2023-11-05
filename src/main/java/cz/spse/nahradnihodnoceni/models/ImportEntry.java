package cz.spse.nahradnihodnoceni.models;

import com.opencsv.bean.CsvBindByPosition;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.Getter;
import lombok.NoArgsConstructor;

@AllArgsConstructor
@NoArgsConstructor
@Getter
public class ImportEntry {

    @CsvBindByPosition(position = 0)
    private String _class;

    @CsvBindByPosition(position = 1)
    private String studentName;

    @CsvBindByPosition(position = 2)
    private String studentSurname;

    @CsvBindByPosition(position = 3)
    private String subjectAbbreviation;

    @CsvBindByPosition(position = 4)
    private String mark;

    @CsvBindByPosition(position = 5)
    private String examinerName;

    @CsvBindByPosition(position = 6)
    private String examinerSurname;

    public int getClassRelativeYear() throws Exception {
        return Integer.parseInt(this.splitClass()[0]);
    }

    public String getClassLabel() throws Exception {
        return this.splitClass()[1];
    }

    private String[] splitClass() throws Exception {
        var components = _class.split("\\.");
        if (components.length != 2)
            throw new Exception(String.format("failed to split the class name: %s", _class));
        return components;
    }
}
