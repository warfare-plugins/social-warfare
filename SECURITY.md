# Security Policy

## Supported Versions

The following versions of this project are currently being supported with security updates.

| Version  | Supported          |
| -------  | ------------------ |
| >=4.5.0  | :white_check_mark: |
| 4.4.8    | :white_check_mark: |
| <4.4.7   | :x:                |

## Reporting a Vulnerability

To report a security issue please email details to info@warfareplugins.com with a descriptive subject line.  In addition, please include the following information along with your report:

- Your name and affiliation (if any).
- A description of the technical details of the vulnerability.  It is very important to let us know how we can reproduce your findings.
- An explanation who can exploit this vulnerability, and what they gain when doing so -- write an attack scenario.  This will help us evaluate your report quickly, especially if the issue is complex.
- Whether this vulnerability is public or known to third parties. If it is, please provide details.

If you believe that an existing (public) issue is security-related, please send an email to info@warfareplugins.com.  The email should include the issue ID and a short description of why it should be handled according to this security policy.

## Responding to Vulnerability Reports

Warfare Plugins takes security bugs seriously.  We appreciate your efforts to responsibly disclose your findings, and will make every effort to acknowledge your contributions.

Your email will be acknowledged within ONE business day, and you will receive a more detailed response to your email within SEVEN days indicating the next steps in handling your report.  After the initial reply to your report, Warfare Plugins will keep you informed of the progress being made towards a fix and announcement.  If your vulnerability report is accepted, then we will work with you on a fix and follow the process noted below on [disclosing vulnerabilities](#disclosing-a-vulnerability).  If your vulnerability report is declined, then we will provide you with a reasoning as to why we came to that conclusion.

## Disclosing a Vulnerability

Once an issue is reported, Warfare Plugins uses the following disclosure process:

- When a report is received, we confirm the issue and determine its severity.
- If we know of specific third-party services or software that require mitigation before publication, those projects will be notified.
- An advisory is prepared (but not published) which details the problem and steps for mitigation.
- Wherever possible, fixes are prepared for the last minor release of the two latest major releases, as well as the trunk branch.  We will attempt to commit these fixes as soon as possible, and as close together as possible.
- Patch releases are published for all fixed released versions and the advisory is published.
- Release notes and our CHANGELOG.md will include a `Security` section with a link to the advisory.

We credit reporters for identifying vulnerabilities, although we will keep your name confidential if you request it.