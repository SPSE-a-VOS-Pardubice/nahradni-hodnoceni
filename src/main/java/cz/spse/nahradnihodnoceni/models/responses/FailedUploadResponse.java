package cz.spse.nahradnihodnoceni.models.responses;

import lombok.AllArgsConstructor;
import lombok.Getter;

import java.util.Set;

@AllArgsConstructor
@Getter
public class FailedUploadResponse {
    private Set<String> missingSubjects;
    private Set<String> missingExaminers;
}
